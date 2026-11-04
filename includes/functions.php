<?php
// الدوال المشتركة بين صفحات الموقع ولوحة التحكم.

const UPLOADS_DIR = __DIR__ . '/../uploads';
const MAX_PLACE_IMAGES = 10;
const MAX_IMAGE_BYTES = 5 * 1024 * 1024;
const ALLOWED_IMAGE_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

// ===================== عامة =====================

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// الاتصال بقاعدة البيانات، مع صفحة خطأ واضحة إذا تعذّر الاتصال
function db() {
    try {
        return yanbu_db();
    } catch (mysqli_sql_exception $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        yanbu_render_unavailable($e->getMessage());
    }
}

// رابط ملف ثابت داخل assets/ مع رقم نسخة يتغيّر عند تعديل الملف (حتى لا يعرض المتصفح نسخة قديمة)
function asset($path, $base = '') {
    $file = __DIR__ . '/../assets/' . $path;
    $version = is_file($file) ? substr(md5_file($file), 0, 8) : '';
    return $base . 'assets/' . $path . ($version !== '' ? '?v=' . $version : '');
}

// رابط صورة مكان مرفوعة، أو الصورة الافتراضية عند عدم وجود صورة
function image_url($filename, $base = '') {
    $filename = basename((string)$filename);
    if ($filename === '') return $base . 'assets/img/placeholder.svg';
    return $base . 'uploads/' . rawurlencode($filename);
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// المسار المطلق لجذر الموقع (مثل /yanbu-tourism/) لروابط يجب ألا تعتمد على مكان الصفحة الحالية
function site_root() {
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    if (basename($dir) === 'admin') $dir = dirname($dir);
    return rtrim($dir, '/') . '/';
}

function excerpt($text, $length = 100) {
    $text = trim(preg_replace('/\s+/u', ' ', (string)$text));
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

function valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// يقبل روابط http/https فقط (يمنع روابط javascript: وغيرها)
function valid_http_url($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false && preg_match('#^https?://#i', $url) === 1;
}

// ===================== التصنيفات =====================

// تصنيف الفئة ضمن الأنواع الأربعة الرئيسية لاختيار الأيقونة واللون المناسبين
function category_type($categoryName) {
    $map = [
        'بحرية' => ['بحر', 'شاطئ', 'شواطئ', 'كورنيش', 'مارينا'],
        'تاريخية' => ['تاريخ', 'معلم', 'معالم', 'أثر', 'قلعة', 'تراث'],
        'طبيعية' => ['طبيع', 'جبل', 'جبال', 'حديقة', 'واحة'],
        'ترفيهية' => ['ترفيه', 'مطعم', 'مطاعم', 'فندق', 'فنادق', 'منتزه'],
    ];
    foreach ($map as $type => $keywords) {
        foreach ($keywords as $keyword) {
            if (mb_strpos((string)$categoryName, $keyword) !== false) return $type;
        }
    }
    return 'عام';
}

function category_icon($categoryName) {
    $icons = ['بحرية' => '🏖️', 'تاريخية' => '🏛️', 'طبيعية' => '🏔️', 'ترفيهية' => '🎡', 'عام' => '📍'];
    return $icons[category_type($categoryName)];
}

// اسم كلاس CSS لتلوين شارات وبطاقات الفئة
function category_slug($categoryName) {
    $slugs = ['بحرية' => 'sea', 'تاريخية' => 'history', 'طبيعية' => 'nature', 'ترفيهية' => 'fun', 'عام' => 'general'];
    return $slugs[category_type($categoryName)];
}

// ===================== خرائط قوقل =====================

function maps_embed_url($placeName, $location) {
    return 'https://www.google.com/maps?q=' . urlencode(trim($placeName . ' ' . $location . ' ينبع')) . '&output=embed';
}

function maps_open_url($googleMapsUrl, $placeName, $location) {
    if (!empty($googleMapsUrl) && valid_http_url($googleMapsUrl)) return $googleMapsUrl;
    return 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($placeName . ' ' . $location . ' ينبع'));
}

// ===================== رفع الصور =====================

// عند تجاوز حجم الطلب الحد المسموح، يفرّغ PHP بيانات النموذج بالكامل؛ نكتشف ذلك لنعرض رسالة واضحة
function upload_too_large() {
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && empty($_POST) && empty($_FILES)
        && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
}

// يتحقق من الصور المرفوعة في حقل images[] ويعيد [قائمة الصور الصالحة, رسالة الخطأ]
function collect_uploaded_images($existingCount = 0) {
    $files = $_FILES['images'] ?? null;
    if (!$files || !is_array($files['name']) || ($files['name'][0] ?? '') === '') return [[], ''];

    $valid = [];
    foreach ($files['name'] as $i => $originalName) {
        $error = $files['error'][$i];
        if ($error === UPLOAD_ERR_NO_FILE) continue;
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return [[], 'الصورة "' . $originalName . '" أكبر من الحجم المسموح.'];
        }
        if ($error !== UPLOAD_ERR_OK) return [[], 'تعذّر رفع الصورة "' . $originalName . '". حاول مرة أخرى.'];

        $info = @getimagesize($files['tmp_name'][$i]);
        $mime = $info['mime'] ?? '';
        if (!isset(ALLOWED_IMAGE_TYPES[$mime])) {
            return [[], 'الصورة "' . $originalName . '" يجب أن تكون JPG أو PNG أو WEBP.'];
        }
        if ($files['size'][$i] > MAX_IMAGE_BYTES) {
            return [[], 'الصورة "' . $originalName . '" أكبر من 5MB.'];
        }
        $valid[] = [
            'tmp' => $files['tmp_name'][$i],
            'name' => 'place_' . bin2hex(random_bytes(10)) . '.' . ALLOWED_IMAGE_TYPES[$mime],
        ];
    }
    if ($existingCount + count($valid) > MAX_PLACE_IMAGES) {
        return [[], 'الحد الأقصى ' . MAX_PLACE_IMAGES . ' صور لكل مكان.'];
    }
    return [$valid, ''];
}

// يحفظ الصور في مجلد uploads ويسجّلها في قاعدة البيانات (أول صورة تصبح رئيسية إن لم توجد رئيسية)
function save_place_images(mysqli $conn, $placeId, array $images) {
    if (!$images) return;
    if (!is_dir(UPLOADS_DIR)) mkdir(UPLOADS_DIR, 0755, true);

    $check = $conn->prepare('SELECT 1 FROM images WHERE place_id = ? AND is_main = 1 LIMIT 1');
    $check->bind_param('i', $placeId);
    $check->execute();
    $hasMain = (bool)$check->get_result()->fetch_row();

    $insert = $conn->prepare('INSERT INTO images (place_id, image_path, is_main) VALUES (?, ?, ?)');
    foreach ($images as $image) {
        if (!move_uploaded_file($image['tmp'], UPLOADS_DIR . '/' . $image['name'])) continue;
        $isMain = $hasMain ? 0 : 1;
        $insert->bind_param('isi', $placeId, $image['name'], $isMain);
        $insert->execute();
        $hasMain = true;
    }
}

function delete_upload_file($filename) {
    $filename = basename((string)$filename);
    $file = UPLOADS_DIR . '/' . $filename;
    if ($filename !== '' && is_file($file)) @unlink($file);
}

// ===================== الجلسة والأمان =====================

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// يرفض أي طلب POST لا يحمل رمز الحماية الصحيح (حماية من CSRF)
function verify_csrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(400);
        exit('انتهت صلاحية الصفحة. ارجع وحدّث الصفحة ثم حاول مرة أخرى.');
    }
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function is_admin() {
    return isset($_SESSION['admin_id']);
}

function require_admin() {
    if (!is_admin()) redirect('login.php');
}
