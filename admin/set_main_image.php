<?php
require __DIR__ . '/partials/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('places.php');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$query = $conn->prepare('SELECT place_id FROM images WHERE id = ?');
$query->bind_param('i', $id);
$query->execute();
$image = $query->get_result()->fetch_assoc();
if (!$image) {
    flash('الصورة غير موجودة.', 'error');
    redirect('places.php');
}
$placeId = (int)$image['place_id'];

$update = $conn->prepare('UPDATE images SET is_main = (id = ?) WHERE place_id = ?');
$update->bind_param('ii', $id, $placeId);
$update->execute();

flash('تم تغيير الصورة الرئيسية ✓');
redirect('edit_place.php?id=' . $placeId);
