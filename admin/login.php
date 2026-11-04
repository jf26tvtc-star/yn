<?php
require __DIR__ . '/../includes/bootstrap.php';

if (is_admin()) redirect('dashboard.php');

const MAX_LOGIN_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 60;

$error = '';
$username = '';
$lockedFor = max(0, ($_SESSION['login_locked_until'] ?? 0) - time());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($lockedFor > 0) {
        $error = 'محاولات كثيرة خاطئة. حاول مرة أخرى بعد ' . $lockedFor . ' ثانية.';
    } elseif ($username === '' || $password === '') {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        $conn = db();
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? AND role = 'admin' LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            unset($_SESSION['login_attempts'], $_SESSION['login_locked_until']);
            $_SESSION['admin_id'] = (int)$admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php');
        }

        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_locked_until'] = time() + LOGIN_LOCK_SECONDS;
        }
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}

$base = '../';
$pageTitle = 'دخول لوحة التحكم';
require __DIR__ . '/../includes/header.php';
?>
        <section class="container login-section">
            <form method="post" class="login-form" autocomplete="on">
                <h1>🔐 دخول لوحة التحكم</h1>
                <p class="section-sub">أدخل اسم المستخدم وكلمة المرور للمتابعة</p>
                <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= e($error) ?></div><?php endif; ?>
                <?= csrf_field() ?>
                <label for="username">اسم المستخدم</label>
                <input type="text" id="username" name="username" value="<?= e($username) ?>" autocomplete="username" required autofocus>
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
                <button type="submit" class="btn">دخول</button>
            </form>
        </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
