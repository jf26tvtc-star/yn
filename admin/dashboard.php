<?php
require __DIR__ . '/partials/init.php';

$placesCount = (int)$conn->query('SELECT COUNT(*) FROM places')->fetch_row()[0];
$unreadCount = (int)$conn->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetch_row()[0];
$messagesCount = (int)$conn->query('SELECT COUNT(*) FROM messages')->fetch_row()[0];
$categories = $conn->query('SELECT c.id, c.name, COUNT(p.id) AS places_count FROM categories c LEFT JOIN places p ON p.category_id = c.id GROUP BY c.id, c.name ORDER BY places_count DESC, c.id')->fetch_all(MYSQLI_ASSOC);
$categoriesCount = count($categories);
$placesWithImages = (int)$conn->query("SELECT COUNT(*) FROM places p WHERE EXISTS (SELECT 1 FROM images i WHERE i.place_id = p.id AND TRIM(i.image_path) <> '')")->fetch_row()[0];
$imageCoverage = $placesCount > 0 ? (int)round($placesWithImages * 100 / $placesCount) : 0;
$recentMessages = $conn->query('SELECT name, subject, is_read, created_at FROM messages ORDER BY created_at DESC, id DESC LIMIT 3')->fetch_all(MYSQLI_ASSOC);

// البحث والتصفية في جدول آخر الأماكن
$search = trim((string)($_GET['q'] ?? ''));
$categoryId = (int)($_GET['category'] ?? 0);
if (!in_array($categoryId, array_map('intval', array_column($categories, 'id')), true)) $categoryId = 0;
$hasFilters = $search !== '' || $categoryId > 0;
$like = '%' . $search . '%';
$stmt = $conn->prepare("SELECT p.id, p.name, p.location, p.created_at, c.name AS category,
    COALESCE((SELECT image_path FROM images WHERE place_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1), '') AS image
    FROM places p JOIN categories c ON c.id = p.category_id
    WHERE (? = '' OR p.name LIKE ? OR p.location LIKE ?) AND (? = 0 OR p.category_id = ?)
    ORDER BY p.created_at DESC, p.id DESC LIMIT 6");
$stmt->bind_param('sssii', $search, $like, $like, $categoryId, $categoryId);
$stmt->execute();
$recentPlaces = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'نظرة عامة';
$activeNav = 'dashboard';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">كل التفاصيل، في مكان واحد</p><h1>لوحة التحكم</h1><p>نظرة شاملة على وجهات ينبع وتواصل زوارك.</p></div>
                <span class="dash-date"><?= admin_icon('calendar') ?> <time datetime="<?= date('Y-m-d') ?>"><?= date('Y/m/d') ?></time></span>
            </div>

            <section class="dash-welcome" aria-labelledby="welcome-title">
                <div class="dash-welcome-content">
                    <span class="dash-welcome-label"><span></span> مساحة إدارة روح ينبع</span>
                    <h2 id="welcome-title">أهلًا بك، رحلة أجمل تبدأ من هنا</h2>
                    <p>أدر وجهاتك، نظّم محتواك، وابقَ قريبًا من زوارك.<br>كل ما تحتاجه لتُظهر ينبع بأجمل صورة.</p>
                    <div class="dash-welcome-actions">
                        <a href="add_place.php" class="dash-button dash-button-gold"><?= admin_icon('plus') ?> إضافة مكان جديد</a>
                        <a href="../index.php" class="dash-hero-link" target="_blank" rel="noopener">استعراض الموقع <?= admin_icon('arrow') ?></a>
                    </div>
                </div>
                <div class="dash-coast-art" aria-hidden="true"><span class="dash-sun"></span><span class="dash-orbit orbit-one"></span><span class="dash-orbit orbit-two"></span><span class="dash-art-pin"><?= admin_icon('pin') ?></span><span class="dash-art-caption">ينبع<span>بحر، تراث، وحياة</span></span></div>
            </section>

            <section class="dash-stats" aria-label="إحصائيات الموقع">
                <a class="dash-stat" href="places.php"><span class="dash-stat-icon tone-green"><?= admin_icon('pin') ?></span><span class="dash-stat-label">إجمالي الأماكن</span><strong><?= number_format($placesCount) ?></strong><span class="dash-stat-foot">وجهات متاحة للاستكشاف <?= admin_icon('arrow') ?></span></a>
                <a class="dash-stat" href="categories.php"><span class="dash-stat-icon tone-gold"><?= admin_icon('tag') ?></span><span class="dash-stat-label">التصنيفات</span><strong><?= number_format($categoriesCount) ?></strong><span class="dash-stat-foot">تنوع يُلهم رحلة الزائر <?= admin_icon('arrow') ?></span></a>
                <a class="dash-stat" href="messages.php"><span class="dash-stat-icon tone-blue"><?= admin_icon('mail') ?></span><span class="dash-stat-label">رسائل غير مقروءة</span><strong><?= number_format($unreadCount) ?></strong><span class="dash-stat-foot"><?= $unreadCount > 0 ? 'بانتظار اطلاعك وردّك' : 'أنت مطّلع على كل الرسائل' ?> <?= admin_icon('arrow') ?></span></a>
                <a class="dash-stat" href="messages.php"><span class="dash-stat-icon tone-purple"><?= admin_icon('mail') ?></span><span class="dash-stat-label">إجمالي الرسائل</span><strong><?= number_format($messagesCount) ?></strong><span class="dash-stat-foot">كل تواصل يصنع فرقًا <?= admin_icon('arrow') ?></span></a>
            </section>

            <section class="dash-quick-actions" aria-label="إجراءات سريعة">
                <strong>وصول سريع</strong>
                <a href="add_place.php"><?= admin_icon('plus') ?> إضافة مكان</a>
                <a href="places.php"><?= admin_icon('pin') ?> إدارة الأماكن</a>
                <a href="categories.php"><?= admin_icon('tag') ?> إدارة التصنيفات</a>
                <a href="messages.php"><?= admin_icon('mail') ?> متابعة الرسائل</a>
            </section>

            <div class="dash-content-grid">
                <section class="dash-panel dash-places-panel" aria-labelledby="places-title">
                    <div class="dash-panel-heading">
                        <div><h2 id="places-title"><?= $hasFilters ? 'نتائج البحث عن الأماكن' : 'آخر الأماكن المضافة' ?></h2><p>عرض أحدث 6 أماكن<?= $hasFilters ? ' مطابقة لبحثك' : ' في دليل روح ينبع' ?></p></div>
                        <a class="dash-text-link" href="places.php">عرض الكل <?= admin_icon('arrow') ?></a>
                    </div>
                    <form class="dash-search" method="get" action="dashboard.php#places-title" role="search">
                        <div class="dash-search-input"><?= admin_icon('search') ?><label class="dash-sr-only" for="place-search">اسم المكان أو الموقع</label><input id="place-search" type="search" name="q" value="<?= e($search) ?>" placeholder="ابحث باسم المكان أو الموقع..."></div>
                        <label class="dash-sr-only" for="category-filter">التصنيف</label>
                        <select id="category-filter" name="category">
                            <option value="0">كل التصنيفات</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>"<?= $categoryId === (int)$category['id'] ? ' selected' : '' ?>><?= e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="dash-button dash-button-primary" type="submit">بحث</button>
                        <?php if ($hasFilters): ?><a class="dash-reset" href="dashboard.php#places-title">مسح</a><?php endif; ?>
                    </form>
                    <?php if ($recentPlaces): ?>
                        <div class="dash-table-wrap">
                            <table class="dash-table">
                                <thead><tr><th scope="col">المكان</th><th scope="col">التصنيف</th><th scope="col">تاريخ الإضافة</th><th scope="col">الإجراءات</th></tr></thead>
                                <tbody>
                                    <?php foreach ($recentPlaces as $place): ?>
                                        <tr>
                                            <td><div class="dash-place-cell"><img src="<?= e(image_url($place['image'], '../')) ?>" alt="" width="46" height="46" loading="lazy"><div><a href="../details.php?id=<?= (int)$place['id'] ?>" target="_blank" rel="noopener"><?= e($place['name']) ?></a><small><?= e($place['location']) ?></small></div></div></td>
                                            <td><span class="dash-category-badge"><?= e($place['category']) ?></span></td>
                                            <td><time datetime="<?= e(date('Y-m-d', strtotime($place['created_at']))) ?>"><?= e(date('Y/m/d', strtotime($place['created_at']))) ?></time></td>
                                            <td><div class="dash-row-actions"><a href="edit_place.php?id=<?= (int)$place['id'] ?>" aria-label="تعديل <?= e($place['name']) ?>" title="تعديل"><?= admin_icon('edit') ?></a><?= delete_button('delete_place.php', ['id' => $place['id']], 'هل أنت متأكد من حذف "' . $place['name'] . '" وصوره؟', 'حذف ' . $place['name'], true) ?></div></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="dash-empty"><span><?= admin_icon($hasFilters ? 'search' : 'pin') ?></span><h3><?= $hasFilters ? 'لا توجد أماكن مطابقة' : 'أول وجهة تبدأ معك' ?></h3><p><?= $hasFilters ? 'جرّب اسمًا آخر أو اختر تصنيفًا مختلفًا.' : 'أضف أول مكان سياحي ليبدأ الزوار باكتشاف جمال ينبع.' ?></p><a class="dash-button dash-button-primary" href="<?= $hasFilters ? 'dashboard.php#places-title' : 'add_place.php' ?>"><?= $hasFilters ? 'عرض أحدث الأماكن' : 'إضافة أول مكان' ?></a></div>
                    <?php endif; ?>
                </section>

                <section class="dash-panel dash-categories-panel" aria-labelledby="categories-title">
                    <div class="dash-panel-heading"><div><h2 id="categories-title">توزيع الوجهات</h2><p>الأماكن حسب التصنيف</p></div><span class="dash-panel-symbol"><?= admin_icon('tag') ?></span></div>
                    <div class="dash-category-total"><strong><?= number_format($placesCount) ?></strong><span>مكان سياحي في <?= $categoriesCount ?> تصنيفات</span></div>
                    <div class="dash-category-list">
                        <?php foreach ($categories as $category): $percentage = $placesCount > 0 ? (int)round((int)$category['places_count'] * 100 / $placesCount) : 0; ?>
                            <a class="dash-category-item" href="dashboard.php?category=<?= (int)$category['id'] ?>#places-title"><span class="dash-category-info"><span><?= e($category['name']) ?></span><strong><?= (int)$category['places_count'] ?> <small>مكان</small></strong></span><span class="dash-progress" aria-hidden="true"><span style="width: <?= $percentage ?>%"></span></span></a>
                        <?php endforeach; ?>
                        <?php if ($categoriesCount === 0): ?><p class="dash-muted">لا توجد تصنيفات بعد. أضف تصنيفًا لتبدأ بتنظيم وجهاتك.</p><?php endif; ?>
                    </div>
                    <a class="dash-bottom-link" href="categories.php">إدارة التصنيفات <?= admin_icon('arrow') ?></a>
                </section>

                <section class="dash-panel dash-messages-panel" aria-labelledby="messages-title">
                    <div class="dash-panel-heading"><div><h2 id="messages-title">آخر رسائل الزوار</h2><p>ابقَ على تواصل مع جمهورك</p></div><a class="dash-text-link" href="messages.php">كل الرسائل <?= admin_icon('arrow') ?></a></div>
                    <div class="dash-message-list">
                        <?php foreach ($recentMessages as $message): ?>
                            <a class="dash-message" href="messages.php"><span class="dash-message-avatar"><?= admin_icon('mail') ?></span><span class="dash-message-text"><strong><?= e($message['name']) ?><?php if (!(int)$message['is_read']): ?><span class="dash-unread">جديدة</span><?php endif; ?></strong><span><?= e($message['subject'] !== '' ? $message['subject'] : 'رسالة بدون عنوان') ?></span></span><time datetime="<?= e(date('c', strtotime($message['created_at']))) ?>"><?= e(date('Y/m/d', strtotime($message['created_at']))) ?></time></a>
                        <?php endforeach; ?>
                        <?php if (!$recentMessages): ?><div class="dash-small-empty"><?= admin_icon('mail') ?><p>لا توجد رسائل بعد. ستظهر رسائل الزوار هنا عند وصولها.</p></div><?php endif; ?>
                    </div>
                </section>

                <section class="dash-panel dash-quality-panel" aria-labelledby="quality-title">
                    <div class="dash-panel-heading"><div><h2 id="quality-title">صورة أجمل لوجهاتك</h2><p>تغطية الأماكن بالصور</p></div><span class="dash-panel-symbol"><?= admin_icon('image') ?></span></div>
                    <div class="dash-coverage"><strong><?= $imageCoverage ?><small>%</small></strong><span><?= $placesWithImages ?> من <?= $placesCount ?> أماكن لديها صور</span></div>
                    <progress class="dash-quality-progress" max="100" value="<?= $imageCoverage ?>" aria-label="نسبة الأماكن التي تحتوي على صور"><?= $imageCoverage ?>%</progress>
                    <p class="dash-quality-hint"><?= admin_icon($placesCount > 0 && $placesWithImages === $placesCount ? 'check' : 'image') ?><?= $placesCount === 0 ? 'أضف أول وجهة مع صورها لتمنح الزائر تجربة أغنى.' : ($placesWithImages === $placesCount ? 'رائع! جميع وجهاتك مدعومة بصور.' : 'لديك ' . ($placesCount - $placesWithImages) . ' أماكن بدون صور. أكملها لتُلهم زوارك.') ?></p>
                    <a class="dash-bottom-link" href="<?= $placesCount === 0 ? 'add_place.php' : 'places.php' ?>"><?= $placesCount === 0 ? 'إضافة وجهة جديدة' : 'إدارة صور الأماكن' ?> <?= admin_icon('arrow') ?></a>
                </section>
            </div>
<?php require __DIR__ . '/partials/footer.php'; ?>
