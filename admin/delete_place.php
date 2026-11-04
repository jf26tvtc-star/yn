<?php
require __DIR__ . '/partials/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('places.php');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$query = $conn->prepare('SELECT image_path FROM images WHERE place_id = ?');
$query->bind_param('i', $id);
$query->execute();
$files = array_column($query->get_result()->fetch_all(MYSQLI_ASSOC), 'image_path');

// حذف المكان يحذف سجلات صوره تلقائيًا (ON DELETE CASCADE)، ثم نحذف ملفات الصور
$delete = $conn->prepare('DELETE FROM places WHERE id = ?');
$delete->bind_param('i', $id);
$delete->execute();
if ($delete->affected_rows > 0) {
    foreach ($files as $file) delete_upload_file($file);
    flash('تم حذف المكان بنجاح ✓');
} else {
    flash('المكان غير موجود، ربما تم حذفه مسبقًا.', 'error');
}

redirect('places.php');
