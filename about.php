<?php
require __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'من نحن';
$activeNav = 'about';
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <h1>من نحن</h1>
                <p>روح ينبع — دليل يساعدك على اكتشاف ينبع والتخطيط لزيارتك.</p>
            </div>
        </section>

        <section class="container">
            <h2>عن روح ينبع</h2>
            <p class="section-note">نجمع الوجهات والأماكن المسجلة في دليل واحد، ونعرّفك على ينبع البحر وينبع النخل وينبع الصناعية لتختار ما يناسب اهتماماتك.</p>
            <div class="category-grid">
                <article class="category-card">
                    <div class="cc-body">
                        <h3>اكتشف</h3>
                        <p>تصفح الأماكن حسب تصنيفها، واطلع على صورها وتفاصيلها وموقعها عند توفرها.</p>
                        <a class="btn btn-sm" href="places.php">اكتشف الأماكن</a>
                    </div>
                </article>
                <article class="category-card">
                    <div class="cc-body">
                        <h3>خطط</h3>
                        <p>استلهم أفكاراً لزيارتك ورتب وقتك بين البحر والطبيعة والتراث.</p>
                        <a class="btn btn-sm" href="trip.php">خطط رحلتك</a>
                    </div>
                </article>
                <article class="category-card">
                    <div class="cc-body">
                        <h3>تواصل</h3>
                        <p>شاركنا استفساراتك وملاحظاتك عبر صفحة التواصل.</p>
                        <a class="btn btn-sm" href="contact.php">تواصل معنا</a>
                    </div>
                </article>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
