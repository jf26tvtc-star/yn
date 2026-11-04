<?php
require __DIR__ . '/includes/bootstrap.php';

$destinations = [
    'sea' => [
        'name' => 'ينبع البحر',
        'intro' => 'اكتشف الوجه الساحلي لينبع، بين البحر الأحمر وأجواء المدينة القديمة.',
        'activities' => [
            ['title' => 'البحر والكورنيش', 'text' => 'خصص وقتاً للمشي على الواجهة البحرية والاستمتاع بمنظر البحر، خاصة وقت الغروب.'],
            ['title' => 'التراث والأسواق', 'text' => 'تعرّف على أجواء ينبع التاريخية وتجول في أحيائها وأسواقها القديمة.'],
            ['title' => 'جلسة على الساحل', 'text' => 'اختم جولتك بوجبة أو جلسة قريبة من البحر، وتحقق من مواعيد المكان قبل الزيارة.'],
        ],
    ],
    'nakhl' => [
        'name' => 'ينبع النخل',
        'intro' => 'وجهة لمحبي الطبيعة والهدوء، تجمع أجواء القرى والنخيل والتراث.',
        'activities' => [
            ['title' => 'الطبيعة والنخيل', 'text' => 'استمتع بمشاهدة الطبيعة واختر وقتاً مناسباً للطقس لجولتك.'],
            ['title' => 'القرى والتراث', 'text' => 'اكتشف ملامح الحياة الريفية والتراث المحلي مع احترام خصوصية السكان والمزارع.'],
            ['title' => 'رحلة هادئة', 'text' => 'جهّز الماء واحتياجات الطريق مسبقاً، وتأكد من مسارك وإمكانية الوصول قبل الانطلاق.'],
        ],
    ],
    'industrial' => [
        'name' => 'ينبع الصناعية',
        'intro' => 'اكتشف الواجهات البحرية والمساحات الخضراء والأجواء العائلية في ينبع الصناعية.',
        'activities' => [
            ['title' => 'الواجهة البحرية', 'text' => 'ابدأ جولتك بالمشي والاستمتاع بإطلالة البحر في الأوقات المعتدلة.'],
            ['title' => 'الحدائق والمساحات الخضراء', 'text' => 'خصص وقتاً للاسترخاء أو نزهة عائلية، وتحقق من تعليمات ومواعيد الموقع الذي تختاره.'],
            ['title' => 'مطاعم وترفيه', 'text' => 'اختر من الأماكن المسجلة في الدليل ما يناسب اهتماماتك ووقت رحلتك.'],
        ],
    ],
];

$area = $_GET['area'] ?? '';
$destination = is_string($area) ? ($destinations[$area] ?? null) : null;
if ($destination === null) {
    require __DIR__ . '/404.php';
    exit();
}

$pageTitle = $destination['name'];
$activeNav = $area;
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <h1><?= e($destination['name']) ?></h1>
                <p><?= e($destination['intro']) ?></p>
            </div>
        </section>

        <section class="container">
            <h2>أفكار لزيارتك</h2>
            <div class="category-grid">
                <?php foreach ($destination['activities'] as $activity): ?>
                    <article class="category-card">
                        <div class="cc-body">
                            <h3><?= e($activity['title']) ?></h3>
                            <p><?= e($activity['text']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="section-note">تظهر الأماكن المسجلة التي يرد اسم المنطقة في اسمها أو وصفها أو موقعها.</p>
            <div class="btn-row">
                <a class="btn" href="places.php?search=<?= urlencode($destination['name']) ?>">اكتشف أماكن <?= e($destination['name']) ?></a>
                <a class="btn btn-secondary" href="trip.php">خطط رحلتك</a>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
