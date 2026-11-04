<?php
require __DIR__ . '/partials/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('places.php');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$query = $conn->prepare('SELECT place_id, image_path, is_main FROM images WHERE id = ?');
$query->bind_param('i', $id);
$query->execute();
$image = $query->get_result()->fetch_assoc();
if (!$image) {
    flash('الصورة غير موجودة.', 'error');
    redirect('places.php');
}
$placeId = (int)$image['place_id'];

$delete = $conn->prepare('DELETE FROM images WHERE id = ?');
$delete->bind_param('i', $id);
$delete->execute();
delete_upload_file($image['image_path']);

// إذا كانت المحذوفة هي الرئيسية، تصبح أقدم صورة متبقية هي الرئيسية
if ($image['is_main']) {
    $next = $conn->prepare('UPDATE images SET is_main = 1 WHERE place_id = ? ORDER BY id ASC LIMIT 1');
    $next->bind_param('i', $placeId);
    $next->execute();
}

flash('تم حذف الصورة ✓');
redirect('edit_place.php?id=' . $placeId);
