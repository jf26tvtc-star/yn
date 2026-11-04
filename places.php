<?php
require __DIR__ . '/includes/bootstrap.php';
$conn = db();

$category = trim((string)($_GET['category'] ?? ''));
$search = trim((string)($_GET['search'] ?? ''));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

$where = [];
$types = '';
$params = [];
if ($category !== '') {
    $where[] = 'c.name = ?';
    $types .= 's';
    $params[] = $category;
}
if ($search !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ? OR p.location LIKE ?)';
    $like = '%' . $search . '%';
    $types .= 'sss';
    array_push($params, $like, $like, $like);
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$count = $conn->prepare("SELECT COUNT(*) FROM places p JOIN categories c ON c.id = p.category_id$whereSql");
if ($types) $count->bind_param($types, ...$params);
$count->execute();
$totalRows = (int)$count->get_result()->fetch_row()[0];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $conn->prepare("SELECT p.id, p.name, p.location, p.description, c.name AS category,
    COALESCE((SELECT image_path FROM images WHERE place_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1), '') AS image
    FROM places p JOIN categories c ON c.id = p.category_id$whereSql
    ORDER BY p.created_at DESC, p.id DESC LIMIT ? OFFSET ?");
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($types . 'ii', ...$allParams);
$stmt->execute();
$places = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$categories = $conn->query('SELECT name FROM categories ORDER BY id')->fetch_all(MYSQLI_ASSOC);

$pageTitle = $category !== '' ? 'وجهات ' . $category : 'الأماكن';
$activeNav = 'places';
$showExcerpt = true;
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <?php if ($category !== ''): ?>
                    <h1><?= category_icon($category) ?> وجهات <?= e($category) ?></h1>
                    <p>تصفّح كل الأماكن المصنّفة ضمن "<?= e($category) ?>" في ينبع</p>
                <?php else: ?>
                    <h1>استكشف الأماكن</h1>
                    <p>اكتشف كل الأماكن السياحية الرائعة في ينبع</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="container">
            <form class="search-filter" method="get" action="places.php" role="search">
                <input type="search" name="search" placeholder="ابحث عن مكان..." value="<?= e($search) ?>" aria-label="ابحث عن مكان">
                <select name="category" aria-label="التصنيف">
                    <option value="">جميع الفئات</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat['name']) ?>"<?= $category === $cat['name'] ? ' selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">بحث</button>
            </form>

            <?php if ($places): ?>
                <div class="places-grid">
                    <?php foreach ($places as $place): ?>
                        <?php require __DIR__ . '/includes/place-card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-note">لم يتم العثور على نتائج<?= $search !== '' || $category !== '' ? ' — <a href="places.php">عرض كل الأماكن</a>' : '' ?></p>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
                <nav class="pagination" aria-label="الصفحات">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="places.php?<?= e(http_build_query(array_filter(['category' => $category, 'search' => $search, 'page' => $i]))) ?>"
                           class="btn<?= $i === $page ? ' active' : ' btn-secondary' ?>"<?= $i === $page ? ' aria-current="page"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
