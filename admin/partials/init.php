<?php
// يُستدعى في أول كل صفحة من صفحات لوحة التحكم (ما عدا صفحة الدخول): يتحقق من تسجيل الدخول ويجهّز الاتصال.
require __DIR__ . '/../../includes/bootstrap.php';
require_admin();
$conn = db();

// أيقونات SVG بسيطة تُستخدم في لوحة التحكم
function admin_icon($name) {
    $paths = [
        'pin' => '<path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        'tag' => '<path d="m21 13-8 8L3 11V3h8Z"/><circle cx="7.5" cy="7.5" r="1"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="m3 7 9 6 9-6"/>',
        'arrow' => '<path d="M19 12H5m6-6-6 6 6 6"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/>',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="3"/><path d="M7 3v4m10-4v4M3 11h18"/>',
        'image' => '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 5-5 4 4 4-6 5 7"/>',
        'edit' => '<path d="m16 3 5 5-12 12-6 1 1-6Zm-2 2 5 5"/>',
        'trash' => '<path d="M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
    ];
    return '<svg class="dash-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}

// قراءة بيانات نموذج المكان (إضافة/تعديل) والتحقق منها. تعيد [القيم, رسالة الخطأ]
function read_place_form(mysqli $conn) {
    $values = [];
    foreach (['name', 'location', 'description', 'google_maps_url', 'phone'] as $key) {
        $values[$key] = trim((string)($_POST[$key] ?? ''));
    }
    $values['category_id'] = (int)($_POST['category_id'] ?? 0);

    $check = $conn->prepare('SELECT 1 FROM categories WHERE id = ?');
    $check->bind_param('i', $values['category_id']);
    $check->execute();

    if ($values['name'] === '' || $values['location'] === '' || $values['description'] === '') {
        return [$values, 'يرجى تعبئة جميع الحقول المطلوبة (الاسم، التصنيف، الموقع، الوصف)'];
    }
    if (!$check->get_result()->fetch_row()) {
        return [$values, 'يرجى اختيار تصنيف صحيح'];
    }
    if (mb_strlen($values['name']) > 150 || mb_strlen($values['location']) > 200 || mb_strlen($values['phone']) > 30 || mb_strlen($values['google_maps_url']) > 500) {
        return [$values, 'بعض الحقول أطول من المسموح'];
    }
    if ($values['google_maps_url'] !== '' && !valid_http_url($values['google_maps_url'])) {
        return [$values, 'رابط خرائط قوقل يجب أن يكون رابطًا صحيحًا يبدأ بـ https://'];
    }
    return [$values, ''];
}

// زر حذف داخل نموذج POST صغير (مع رمز الحماية ورسالة تأكيد)
function delete_button($action, array $fields, $confirm, $label = 'حذف', $iconOnly = false) {
    $html = '<form class="inline-form" method="post" action="' . e($action) . '" onsubmit="return confirm(' . e(json_encode($confirm, JSON_UNESCAPED_UNICODE)) . ')">' . csrf_field();
    foreach ($fields as $name => $value) {
        $html .= '<input type="hidden" name="' . e($name) . '" value="' . e($value) . '">';
    }
    $content = $iconOnly ? admin_icon('trash') : e($label);
    $aria = $iconOnly ? ' aria-label="' . e($label) . '" title="' . e($label) . '"' : '';
    return $html . '<button type="submit" class="' . ($iconOnly ? 'dash-delete' : 'panel-link is-danger') . '"' . $aria . '>' . $content . '</button></form>';
}
