<?php
require __DIR__ . '/partials/init.php';

$error = '';
$values = ['name' => '', 'description' => ''];

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
            $stmt = $conn->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
            $stmt->bind_param('ss', $values['name'], $values['description']);
            $stmt->execute();
            flash('تمت إضافة التصنيف بنجاح ✓');
            redirect('categories.php');
        } catch (mysqli_sql_exception $e) {
            $error = 'هذا التصنيف موجود مسبقًا';
        }
    }
}

$categories = $conn->query('SELECT c.*, (SELECT COUNT(*) FROM places p WHERE p.category_id = c.id) AS places_count FROM categories c ORDER BY c.id')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'التصنيفات';
$activeNav = 'categories';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">المحتوى</p><h1>التصنيفات</h1><p>نظّم الأماكن في تصنيفات يسهل على الزائر تصفحها.</p></div>
            </div>

            <div class="panel-columns">
                <section class="dash-panel">
                    <div class="dash-panel-heading"><div><h2>كل التصنيفات</h2></div></div>
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th scope="col">الاسم</th><th scope="col">الوصف</th><th scope="col">الأماكن</th><th scope="col">الإجراءات</th></tr></thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                    <tr>
                                        <td><strong><?= category_icon($c['name']) ?> <?= e($c['name']) ?></strong></td>
                                        <td><?= e($c['description']) ?></td>
                                        <td><?= (int)$c['places_count'] ?></td>
                                        <td><div class="panel-row-actions">
                                            <a class="panel-link" href="edit_category.php?id=<?= (int)$c['id'] ?>">تعديل</a>
                                            <?php if ((int)$c['places_count'] > 0): ?>
                                                <button type="button" class="panel-link is-danger" disabled title="لا يمكن حذف تصنيف مرتبط بأماكن">حذف</button>
                                            <?php else: ?>
                                                <?= delete_button('delete_category.php', ['id' => $c['id']], 'حذف تصنيف "' . $c['name'] . '"؟') ?>
                                            <?php endif; ?>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (!$categories): ?><tr><td colspan="4">لا توجد تصنيفات بعد.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <form class="dash-panel panel-form" method="post">
                    <h2>إضافة تصنيف</h2>
                    <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
                    <?= csrf_field() ?>
                    <label>اسم التصنيف *<input name="name" required maxlength="100" value="<?= e($values['name']) ?>"></label>
                    <label>وصف مختصر (اختياري)<input name="description" maxlength="255" value="<?= e($values['description']) ?>"></label>
                    <p class="panel-muted">تُختار الأيقونة واللون تلقائيًا من الاسم (مثل: شواطئ، معالم، جبال، مطاعم).</p>
                    <div class="panel-actions"><button class="dash-button dash-button-primary" type="submit">إضافة</button></div>
                </form>
            </div>
<?php require __DIR__ . '/partials/footer.php'; ?>
