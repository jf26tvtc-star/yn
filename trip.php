<?php
require __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'خطط رحلتك';
$activeNav = 'trip';
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <h1>خطط رحلتك</h1>
                <p>اختر الوجهة التي تناسبك، ثم رتّب زيارتك حسب وقتك واهتماماتك.</p>
            </div>
        </section>

        <section class="container">
            <h2>مسارات مقترحة</h2>
            <p class="section-note">هذه أفكار مرنة للزيارة وليست حجوزات أو برامج سياحية مؤكدة.</p>
            <div class="category-grid">
                <article class="category-card sea">
                    <div class="cc-body">
                        <h3>يوم في ينبع البحر</h3>
                        <p>ابدأ بجولة تراثية، ثم استراحة لتناول الطعام، واختم اليوم بالمشي قرب البحر وقت الغروب.</p>
                        <a class="btn btn-sm" href="destination.php?area=sea">استكشف ينبع البحر</a>
                    </div>
                </article>
                <article class="category-card nature">
                    <div class="cc-body">
                        <h3>جولة في ينبع النخل</h3>
                        <p>انطلق مبكراً لجولة في الطبيعة والقرى، وخذ استراحة خلال النهار، ورتّب العودة وفق حالة الطريق والطقس.</p>
                        <a class="btn btn-sm" href="destination.php?area=nakhl">استكشف ينبع النخل</a>
                    </div>
                </article>
                <article class="category-card fun">
                    <div class="cc-body">
                        <h3>وقت عائلي في ينبع الصناعية</h3>
                        <p>اجمع بين نزهة في المساحات الخضراء وجولة على الواجهة البحرية، ثم اختر مطعماً يناسب العائلة.</p>
                        <a class="btn btn-sm" href="destination.php?area=industrial">استكشف ينبع الصناعية</a>
                    </div>
                </article>
            </div>
        </section>

        <section class="container">
            <h2>قبل الانطلاق</h2>
            <p class="section-note">راجع الطقس ومواعيد العمل، وحدد ميزانيتك ووسيلة التنقل، وتأكد من موقع كل وجهة وإمكانية الوصول إليها. تحقق من أي حجز أو رسوم مباشرة مع مقدم الخدمة.</p>
            <div class="btn-row">
                <a class="btn" href="places.php">اكتشف الأماكن</a>
                <a class="btn btn-secondary" href="contact.php">تواصل معنا للاستفسار</a>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
