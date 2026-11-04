<?php
require __DIR__ . '/partials/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('categories.php');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
try {
    $delete = $conn->prepare('DELETE FROM categories WHERE id = ?');
    $delete->bind_param('i', $id);
    $delete->execute();
    flash($delete->affected_rows > 0 ? 'تم حذف التصنيف بنجاح ✓' : 'التصنيف غير موجود.', $delete->affected_rows > 0 ? 'success' : 'error');
} catch (mysqli_sql_exception $e) {
    // قيد المفتاح الأجنبي يمنع حذف تصنيف مرتبط بأماكن
    flash('لا يمكن حذف هذا التصنيف لأنه مستخدم في أماكن موجودة. انقل تلك الأماكن إلى تصنيف آخر أولًا.', 'error');
}

redirect('categories.php');
