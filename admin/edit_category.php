<?php
require __DIR__ . '/partials/init.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM categories WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
if (!$category) {
    flash('التصنيف غير موجود.', 'error');
    redirect('categories.php');
}

$error = '';
$values = ['name' => $category['name'], 'description' => $category['description']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $values['name'] = trim((string)($_POST['name'] ?? ''));
    $values['description'] = trim((string)($_POST['description'] ?? ''));

    if ($values['name'] === '') {
        $error = 'يرجى إدخال اسم التصنيف';
    } elseif (mb_strlen($values['name']) > 100 || mb_strlen($values['description']) > 255) {
        $error = 'الاسم أو الوصف أطول من المسموح';
    } else {
        try {
            $update = $conn->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
            $update->bind_param('ssi', $values['name'], $values['description'], $id);
            $update->execute();
            flash('تم تعديل التصنيف بنجاح ✓');
            redirect('categories.php');
        } catch (mysqli_sql_exception $e) {
            $error = 'هذا الاسم مستخدم في تصنيف آخر';
        }
    }
}

$pageTitle = 'تعديل تصنيف';
$activeNav = 'categories';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">المحتوى</p><h1>تعديل: <?= e($category['name']) ?></h1></div>
            </div>

            <form class="dash-panel panel-form panel-narrow" method="post">
                <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
                <?= csrf_field() ?>
                <label>اسم التصنيف *<input name="name" required maxlength="100" value="<?= e($values['name']) ?>"></label>
                <label>وصف مختصر (اختياري)<input name="description" maxlength="255" value="<?= e($values['description']) ?>"></label>
                <div class="panel-actions">
                    <button class="dash-button dash-button-primary" type="submit">حفظ التعديلات</button>
                    <a class="dash-button panel-secondary" href="categories.php">إلغاء</a>
                </div>
            </form>
<?php require __DIR__ . '/partials/footer.php'; ?>
