<?php
require __DIR__ . '/partials/init.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM places WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
if (!$place) {
    flash('المكان غير موجود، ربما تم حذفه.', 'error');
    redirect('places.php');
}

function place_images(mysqli $conn, $placeId) {
    $stmt = $conn->prepare('SELECT id, image_path, is_main FROM images WHERE place_id = ? ORDER BY is_main DESC, id ASC');
    $stmt->bind_param('i', $placeId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$error = '';
$values = $place;
$images = place_images($conn, $id);

if (upload_too_large()) {
    $error = 'حجم الصور المرفوعة أكبر من المسموح في إعدادات الخادم. ارفع صورًا أصغر أو عددًا أقل في المرة الواحدة.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$values, $error] = read_place_form($conn);
    if (!$error) [$newImages, $error] = collect_uploaded_images(count($images));

    if (!$error) {
        $update = $conn->prepare('UPDATE places SET name = ?, description = ?, category_id = ?, location = ?, google_maps_url = ?, phone = ? WHERE id = ?');
        $update->bind_param('ssisssi', $values['name'], $values['description'], $values['category_id'], $values['location'], $values['google_maps_url'], $values['phone'], $id);
        $update->execute();
        save_place_images($conn, $id, $newImages);
        flash('تم حفظ التعديلات بنجاح ✓');
        redirect('edit_place.php?id=' . $id);
    }
}

$categories = $conn->query('SELECT id, name FROM categories ORDER BY id')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'تعديل مكان';
$activeNav = 'places';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">المحتوى</p><h1>تعديل: <?= e($place['name']) ?></h1><p>عدّل البيانات ثم اضغط حفظ.</p></div>
                <a class="dash-button panel-secondary" href="../details.php?id=<?= $id ?>" target="_blank" rel="noopener">عرض في الموقع</a>
            </div>

            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>

            <section class="dash-panel panel-form">
                <h2>صور المكان الحالية</h2>
                <?php if (!$images): ?>
                    <p class="panel-muted">لا توجد صور بعد — ستظهر صورة افتراضية. أضف صورًا من النموذج بالأسفل.</p>
                <?php else: ?>
                    <div class="panel-image-list">
                        <?php foreach ($images as $img): ?>
                            <div class="panel-image">
                                <img src="<?= e(image_url($img['image_path'], '../')) ?>" alt="">
                                <?php if ($img['is_main']): ?><span class="panel-badge">رئيسية</span><?php endif; ?>
                                <div>
                                    <?php if (!$img['is_main']): ?>
                                        <form class="inline-form" method="post" action="set_main_image.php">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                                            <button type="submit">اجعلها رئيسية</button>
                                        </form>
                                    <?php endif; ?>
                                    <?= delete_button('delete_image.php', ['id' => $img['id']], 'حذف هذه الصورة نهائيًا؟') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <?php
            $submitLabel = 'حفظ التعديلات';
            $imagesCount = count($images);
            require __DIR__ . '/partials/place-form.php';
            ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
