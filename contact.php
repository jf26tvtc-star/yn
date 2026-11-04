<?php
require __DIR__ . '/includes/bootstrap.php';
$conn = db();

$success = '';
$error = '';
$form = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($form as $key => $_) {
        $form[$key] = trim((string)($_POST[$key] ?? ''));
    }

    if ($form['name'] === '' || $form['email'] === '' || $form['message'] === '') {
        $error = 'يرجى ملء جميع الحقول المطلوبة';
    } elseif (!valid_email($form['email'])) {
        $error = 'صيغة البريد الإلكتروني غير صحيحة';
    } elseif (mb_strlen($form['name']) > 120 || mb_strlen($form['email']) > 190 || mb_strlen($form['phone']) > 30 || mb_strlen($form['subject']) > 190) {
        $error = 'بعض الحقول أطول من المسموح';
    } else {
        $stmt = $conn->prepare('INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $form['name'], $form['email'], $form['phone'], $form['subject'], $form['message']);
        $stmt->execute();
        $success = 'تم استقبال رسالتك بنجاح! سيتم التواصل معك قريباً.';
        $form = array_fill_keys(array_keys($form), '');
    }
}

$pageTitle = 'تواصل معنا';
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <h1>تواصل معنا</h1>
                <p>نحن هنا للإجابة على جميع استفساراتك</p>
            </div>
        </section>

        <section class="container">
            <div class="contact-layout">
                <div class="contact-info">
                    <h2>معلومات التواصل</h2>
                    <div class="info-card">
                        <h3>📍 العنوان</h3>
                        <p>ينبع، المملكة العربية السعودية</p>
                    </div>
                    <div class="info-card">
                        <h3>📱 الهاتف</h3>
                        <p><a href="tel:+96656789455" dir="ltr">+96656789455</a></p>
                    </div>
                    <div class="info-card">
                        <h3>📧 البريد الإلكتروني</h3>
                        <p><a href="mailto:jf26tvtc@gmail.com">jf26tvtc@gmail.com</a></p>
                    </div>
                </div>

                <form method="post" class="contact-form">
                    <h2>أرسل رسالة</h2>
                    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                    <?= csrf_field() ?>
                    <input type="text" name="name" placeholder="اسمك الكامل *" maxlength="120" value="<?= e($form['name']) ?>" required>
                    <input type="email" name="email" placeholder="بريدك الإلكتروني *" maxlength="190" value="<?= e($form['email']) ?>" required>
                    <input type="tel" name="phone" placeholder="رقم الهاتف" maxlength="30" value="<?= e($form['phone']) ?>">
                    <input type="text" name="subject" placeholder="الموضوع" maxlength="190" value="<?= e($form['subject']) ?>">
                    <textarea name="message" placeholder="رسالتك... *" rows="6" required><?= e($form['message']) ?></textarea>
                    <button type="submit" class="btn">إرسال الرسالة 📤</button>
                </form>
            </div>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
