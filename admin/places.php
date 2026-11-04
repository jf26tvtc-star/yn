<?php
require __DIR__ . '/partials/init.php';

$places = $conn->query("SELECT p.id, p.name, p.location, p.created_at, c.name AS category,
    COALESCE((SELECT image_path FROM images WHERE place_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1), '') AS image
    FROM places p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC, p.id DESC")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'إدارة الأماكن';
$activeNav = 'places';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">المحتوى</p><h1>إدارة الأماكن</h1><p><?= count($places) ?> مكان في الدليل</p></div>
                <a class="dash-button dash-button-primary" href="add_place.php"><?= admin_icon('plus') ?> إضافة مكان</a>
            </div>

            <section class="dash-panel">
                <?php if ($places): ?>
                    <div class="dash-table-wrap">
                        <table class="dash-table">
                            <thead><tr><th scope="col">المكان</th><th scope="col">التصنيف</th><th scope="col">تاريخ الإضافة</th><th scope="col">الإجراءات</th></tr></thead>
                            <tbody>
                                <?php foreach ($places as $place): ?>
                                    <tr>
                                        <td><div class="dash-place-cell"><img src="<?= e(image_url($place['image'], '../')) ?>" alt="" width="46" height="46" loading="lazy"><div><a href="../details.php?id=<?= (int)$place['id'] ?>" target="_blank" rel="noopener"><?= e($place['name']) ?></a><small><?= e($place['location']) ?></small></div></div></td>
                                        <td><span class="dash-category-badge"><?= e($place['category']) ?></span></td>
                                        <td><time datetime="<?= e(date('Y-m-d', strtotime($place['created_at']))) ?>"><?= e(date('Y/m/d', strtotime($place['created_at']))) ?></time></td>
                                        <td><div class="panel-row-actions"><a class="panel-link" href="edit_place.php?id=<?= (int)$place['id'] ?>">تعديل</a><?= delete_button('delete_place.php', ['id' => $place['id']], 'هل أنت متأكد من حذف "' . $place['name'] . '" وصوره؟') ?></div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="dash-empty"><span><?= admin_icon('pin') ?></span><h3>لا توجد أماكن</h3><p>أضف أول وجهة ليبدأ الزوار باكتشاف جمال ينبع.</p><a class="dash-button dash-button-primary" href="add_place.php">إضافة مكان</a></div>
                <?php endif; ?>
            </section>
<?php require __DIR__ . '/partials/footer.php'; ?>
