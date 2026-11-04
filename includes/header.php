<?php
// رأس الصفحات العامة + شريط التنقل.
// متغيرات اختيارية قبل الاستدعاء: $pageTitle، $activeNav، $base ('../' للصفحات داخل مجلد فرعي)
$base = $base ?? '';
$activeNav = $activeNav ?? '';
$navLinks = [
    'home' => ['index.php', 'الرئيسية'],
    'sea' => ['destination.php?area=sea', 'ينبع البحر'],
    'nakhl' => ['destination.php?area=nakhl', 'ينبع النخل'],
    'industrial' => ['destination.php?area=industrial', 'ينبع الصناعية'],
    'places' => ['places.php', 'اكتشف الأماكن'],
    'trip' => ['trip.php', 'خطط رحلتك'],
    'about' => ['about.php', 'من نحن'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(!empty($pageTitle) ? $pageTitle . ' - روح ينبع' : 'روح ينبع - اكتشف جمال ينبع') ?></title>
    <meta name="description" content="روح ينبع: دليل سياحي لشواطئ ينبع ومعالمها وطبيعتها ومطاعمها في مكان واحد.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/style.css', $base)) ?>">
</head>
<body>
    <nav class="navbar site-navbar" aria-label="التنقل الرئيسي">
        <div class="container">
            <a class="brand" href="<?= e($base) ?>index.php">🌴 روح ينبع</a>
            <ul>
                <?php foreach ($navLinks as $key => [$href, $label]): ?>
                    <li><a href="<?= e($base . $href) ?>"<?= $key === $activeNav ? ' class="is-active" aria-current="page"' : '' ?>><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>
    <main class="site-main" id="main-content">
