<?php
require __DIR__ . '/includes/bootstrap.php';
$conn = db();

// صورة الهيرو: صورة أحدث مكان مُضاف إن وجدت، وإلا خلفية متدرّجة افتراضية
// (مسار مطلق لأن url() داخل متغير CSS يُحسب نسبةً لملف الـ CSS وليس للصفحة)
$heroRow = $conn->query('SELECT image_path FROM images ORDER BY id DESC LIMIT 1')->fetch_assoc();
$heroStyle = $heroRow ? "--hero-img:url('" . image_url($heroRow['image_path'], site_root()) . "');" : '';

$placesTotal = (int)$conn->query('SELECT COUNT(*) FROM places')->fetch_row()[0];
$imagesTotal = (int)$conn->query('SELECT COUNT(*) FROM images')->fetch_row()[0];
$categories = $conn->query('SELECT c.*, (SELECT COUNT(*) FROM places p WHERE p.category_id = c.id) AS places_count FROM categories c ORDER BY c.id')->fetch_all(MYSQLI_ASSOC);

// الأماكن مجمّعة حسب النوع (بحرية / تاريخية / طبيعية / ترفيهية) لعرضها في أقسام
$allPlaces = $conn->query("SELECT p.id, p.name, p.location, c.name AS category,
    COALESCE((SELECT image_path FROM images WHERE place_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1), '') AS image
    FROM places p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC, p.id DESC");
$sections = [
    'بحرية' => ['title' => '🏖️ وجهات بحرية', 'sub' => 'شواطئ وكورنيش ينبع الساحرة', 'places' => []],
    'تاريخية' => ['title' => '🏛️ معالم تاريخية', 'sub' => 'آثار وتراث يحكي قصة ينبع', 'places' => []],
    'طبيعية' => ['title' => '🏔️ وجهات طبيعية', 'sub' => 'جبال وطبيعة خلّابة', 'places' => []],
    'ترفيهية' => ['title' => '🎡 مطاعم وترفيه', 'sub' => 'أفضل المطاعم والفنادق وأماكن الترفيه', 'places' => []],
];
while ($row = $allPlaces->fetch_assoc()) {
    $type = category_type($row['category']);
    if (isset($sections[$type])) $sections[$type]['places'][] = $row;
}

$activeNav = 'home';
require __DIR__ . '/includes/header.php';
?>
        <section class="hero" style="<?= e($heroStyle) ?>">
            <div class="container">
                <span class="eyebrow">المملكة العربية السعودية</span>
                <h1>اكتشف جمال ينبع</h1>
                <p>شواطئ ساحرة، تاريخ عريق، طبيعة خلّابة، ومطاعم تستحق الزيارة — كل وجهات ينبع في مكان واحد</p>
            </div>
        </section>

        <div class="container">
            <form class="hero-search" action="places.php" method="get" role="search">
                <input type="search" name="search" placeholder="🔍 ابحث عن مكان، شاطئ، مطعم..." aria-label="ابحث عن مكان">
                <select name="category" aria-label="التصنيف">
                    <option value="">كل التصنيفات</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= e($c['name']) ?>"><?= category_icon($c['name']) ?> <?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn">ابحث</button>
            </form>

            <div class="cat-pills">
                <?php foreach ($categories as $c): ?>
                    <a class="cat-pill" href="places.php?category=<?= urlencode($c['name']) ?>">
                        <span class="ico"><?= category_icon($c['name']) ?></span>
                        <?= e($c['name']) ?>
                        <span class="count">(<?= (int)$c['places_count'] ?>)</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php foreach ($sections as $section): if (!$section['places']) continue; ?>
        <section class="container">
            <div class="section-head">
                <div>
                    <h2><?= e($section['title']) ?></h2>
                    <p class="section-sub"><?= e($section['sub']) ?></p>
                </div>
                <a class="see-all" href="places.php?category=<?= urlencode($section['places'][0]['category']) ?>">عرض الكل ←</a>
            </div>
            <div class="scroll-row">
                <?php foreach (array_slice($section['places'], 0, 8) as $place): ?>
                    <?php require __DIR__ . '/includes/place-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>

        <?php if ($placesTotal === 0): ?>
        <section class="container">
            <p class="empty-note">لا توجد أماكن مسجلة حالياً — ابدأ بإضافتها من لوحة التحكم.</p>
        </section>
        <?php endif; ?>

        <section class="container">
            <div class="dashboard-grid">
                <div class="stat-card"><h3>وجهات سياحية</h3><div class="number"><?= $placesTotal ?></div></div>
                <div class="stat-card"><h3>تصنيفات</h3><div class="number"><?= count($categories) ?></div></div>
                <div class="stat-card"><h3>صور</h3><div class="number"><?= $imagesTotal ?></div></div>
                <div class="stat-card"><h3>تقييم الزوار</h3><div class="number">4.5 ⭐</div></div>
            </div>
        </section>

        <section class="container cta-box">
            <h2>خطّط لرحلتك القادمة إلى ينبع</h2>
            <p class="section-sub">تصفّح دليل الوجهات الكامل أو تواصل معنا لأي استفسار</p>
            <div class="btn-row">
                <a href="places.php" class="btn">تصفّح كل الأماكن</a>
                <a href="contact.php" class="btn btn-secondary">تواصل معنا</a>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
