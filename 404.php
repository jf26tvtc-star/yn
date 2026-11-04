<?php
require_once __DIR__ . '/includes/bootstrap.php';

// تُعرض هذه الصفحة للروابط غير الموجودة (عبر .htaccess) أو عند طلب مكان غير موجود.
// نستخدم مسارًا مطلقًا للروابط لأنها قد تُعرض تحت أي رابط داخل الموقع.
http_response_code(404);
$base = site_root();
$pageTitle = 'الصفحة غير موجودة';
require __DIR__ . '/includes/header.php';
?>
        <section class="container not-found">
            <span class="not-found-code">404</span>
            <h1>الصفحة غير موجودة</h1>
            <p>ربما تم نقل الصفحة أو حذفها، أو أن الرابط غير صحيح.</p>
            <div class="btn-row">
                <a class="btn" href="<?= e($base) ?>index.php">العودة للرئيسية</a>
                <a class="btn btn-secondary" href="<?= e($base) ?>places.php">اكتشف الأماكن</a>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
