<?php
// رأس صفحات لوحة التحكم: القائمة الجانبية + الشريط العلوي.
// يتطلب: $pageTitle، و $activeNav (dashboard | places | place-new | categories | messages)
$activeNav = $activeNav ?? '';
$counts = $conn->query('SELECT (SELECT COUNT(*) FROM places) AS places, (SELECT COUNT(*) FROM categories) AS categories,
    (SELECT COUNT(*) FROM messages) AS messages, (SELECT COUNT(*) FROM messages WHERE is_read = 0) AS unread')->fetch_assoc();
$flash = take_flash();
$navAttr = function ($key) use ($activeNav) {
    return $key === $activeNav ? ' class="is-active" aria-current="page"' : '';
};
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle) ?> - لوحة التحكم - روح ينبع</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/style.css', '../')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css', '../')) ?>">
</head>
<body class="dashboard-page">
    <a class="dash-skip" href="#main-content">تجاوز إلى المحتوى</a>
    <aside class="dash-sidebar">
        <a class="dash-brand" href="dashboard.php">روح ينبع<small>لوحة إدارة الوجهات</small></a>
        <nav class="dash-menu" aria-label="القائمة الرئيسية للإدارة">
            <a class="dash-menu-home<?= $activeNav === 'dashboard' ? ' is-active' : '' ?>" href="dashboard.php"<?= $activeNav === 'dashboard' ? ' aria-current="page"' : '' ?>>نظرة عامة</a>
            <details class="dash-menu-group" open>
                <summary>المحتوى</summary>
                <div class="dash-menu-links">
                    <a href="places.php"<?= $navAttr('places') ?>>إدارة الأماكن <span class="dash-nav-count"><?= (int)$counts['places'] ?></span></a>
                    <a href="add_place.php"<?= $navAttr('place-new') ?>>إضافة مكان جديد</a>
                    <a href="categories.php"<?= $navAttr('categories') ?>>التصنيفات <span class="dash-nav-count"><?= (int)$counts['categories'] ?></span></a>
                </div>
            </details>
            <details class="dash-menu-group" open>
                <summary>التواصل <?php if ($counts['unread'] > 0): ?><span class="dash-nav-count is-new"><?= (int)$counts['unread'] ?> جديدة</span><?php endif; ?></summary>
                <div class="dash-menu-links">
                    <a href="messages.php"<?= $navAttr('messages') ?>>رسائل الزوار <span class="dash-nav-count"><?= (int)$counts['messages'] ?></span></a>
                </div>
            </details>
            <details class="dash-menu-group" open>
                <summary>الحساب</summary>
                <div class="dash-menu-links">
                    <a href="../index.php" target="_blank" rel="noopener">زيارة الموقع</a>
                    <a class="dash-menu-logout" href="logout.php">تسجيل الخروج</a>
                </div>
            </details>
        </nav>
    </aside>

    <div class="dash-workspace">
        <header class="dash-topbar">
            <div class="dash-breadcrumb">الإدارة <span>/</span> <strong><?= e($pageTitle) ?></strong></div>
            <div class="dash-topbar-actions">
                <a class="dash-notification" href="messages.php" aria-label="رسائل الزوار، <?= (int)$counts['unread'] ?> غير مقروءة">
                    <?= admin_icon('mail') ?><?php if ($counts['unread'] > 0): ?><span class="dash-notification-dot"></span><?php endif; ?>
                </a>
                <div class="dash-profile">
                    <span class="dash-avatar"><?= e(mb_strtoupper(mb_substr($_SESSION['admin_username'] ?? 'م', 0, 1))) ?></span>
                    <div><strong><?= e($_SESSION['admin_username'] ?? 'المسؤول') ?></strong><small>مدير الموقع</small></div>
                </div>
            </div>
        </header>

        <main class="dash-main" id="main-content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?>" role="status"><?= e($flash['message']) ?></div>
            <?php endif; ?>
