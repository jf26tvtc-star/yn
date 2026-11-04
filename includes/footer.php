<?php
// تذييل موحّد لكل الصفحات العامة (يلتصق بأسفل الشاشة حتى في الصفحات قصيرة المحتوى).
$base = $base ?? '';
?>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <strong>🌴 روح ينبع</strong>
                <p>دليلك لاكتشاف شواطئ ينبع ومعالمها وطبيعتها ومطاعمها في مكان واحد.</p>
            </div>
            <nav class="footer-links" aria-label="روابط سريعة">
                <strong>روابط سريعة</strong>
                <a href="<?= e($base) ?>places.php">اكتشف الأماكن</a>
                <a href="<?= e($base) ?>categories.php">التصنيفات</a>
                <a href="<?= e($base) ?>trip.php">خطط رحلتك</a>
                <a href="<?= e($base) ?>about.php">من نحن</a>
                <a href="<?= e($base) ?>contact.php">تواصل معنا</a>
            </nav>
            <div class="footer-contact">
                <strong>تواصل معنا</strong>
                <a href="mailto:jf26tvtc@gmail.com">📧 jf26tvtc@gmail.com</a>
                <a href="tel:+96656789455">📱 <span dir="ltr">+96656789455</span></a>
                <span>📍 ينبع، المملكة العربية السعودية</span>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> روح ينبع | جميع الحقوق محفوظة</p>
                <a href="<?= e($base) ?>admin/"><?= is_admin() ? 'لوحة التحكم' : 'دخول الإدارة' ?></a>
            </div>
        </div>
    </footer>
    <script src="<?= e(asset('js/main.js', $base)) ?>"></script>
</body>
</html>
