<?php
require __DIR__ . '/partials/init.php';

$error = '';
$values = ['name' => '', 'category_id' => 0, 'location' => '', 'description' => '', 'google_maps_url' => '', 'phone' => ''];

if (upload_too_large()) {
    $error = 'حجم الصور المرفوعة أكبر من المسموح في إعدادات الخادم. ارفع صورًا أصغر أو عددًا أقل في المرة الواحدة.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$values, $error] = read_place_form($conn);
    if (!$error) [$images, $error] = collect_uploaded_images();

    if (!$error) {
        $stmt = $conn->prepare('INSERT INTO places (name, description, category_id, location, google_maps_url, phone) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssisss', $values['name'], $values['description'], $values['category_id'], $values['location'], $values['google_maps_url'], $values['phone']);
        $stmt->execute();
        $placeId = $stmt->insert_id;
        save_place_images($conn, $placeId, $images);
        flash('تمت إضافة المكان بنجاح ✓');
        redirect('edit_place.php?id=' . $placeId);
    }
}

$categories = $conn->query('SELECT id, name FROM categories ORDER BY id')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'إضافة مكان';
$activeNav = 'place-new';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">المحتوى</p><h1>إضافة مكان جديد</h1><p>أدخل بيانات الوجهة وصورها.</p></div>
            </div>

            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>

            <?php if (!$categories): ?>
                <div class="dash-panel dash-empty"><h3>أضف تصنيفًا أولًا</h3><p>يجب أن يكون هناك تصنيف واحد على الأقل قبل إضافة الأماكن.</p><a class="dash-button dash-button-primary" href="categories.php">إدارة التصنيفات</a></div>
            <?php else:
                $submitLabel = 'إضافة المكان';
                $imagesCount = 0;
                require __DIR__ . '/partials/place-form.php';
            endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
