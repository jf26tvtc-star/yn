<?php
require __DIR__ . '/includes/bootstrap.php';
$conn = db();

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT p.*, c.name AS category FROM places p JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
if (!$place) {
    require __DIR__ . '/404.php';
    exit();
}

// كل صور المكان (الرئيسية أولًا) لمعرض الصور
$imgStmt = $conn->prepare('SELECT image_path FROM images WHERE place_id = ? ORDER BY is_main DESC, id ASC');
$imgStmt->bind_param('i', $id);
$imgStmt->execute();
$images = array_column($imgStmt->get_result()->fetch_all(MYSQLI_ASSOC), 'image_path');
if (!$images) $images = [''];

$mapsEmbed = maps_embed_url($place['name'], $place['location']);
$mapsUrl = maps_open_url($place['google_maps_url'] ?? '', $place['name'], $place['location']);
$catSlug = category_slug($place['category']);

// أماكن مشابهة من نفس الفئة
$similarStmt = $conn->prepare("SELECT p.id, p.name, p.location, c.name AS category,
    COALESCE((SELECT image_path FROM images WHERE place_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1), '') AS image
    FROM places p JOIN categories c ON c.id = p.category_id
    WHERE p.category_id = ? AND p.id <> ? ORDER BY p.created_at DESC LIMIT 4");
$similarStmt->bind_param('ii', $place['category_id'], $id);
$similarStmt->execute();
$similar = $similarStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pageTitle = $place['name'];
$activeNav = 'places';
require __DIR__ . '/includes/header.php';
?>
        <div class="breadcrumb">
            <div class="container">
                <a href="index.php">الرئيسية</a> /
                <a href="places.php">الأماكن</a> /
                <span><?= e($place['name']) ?></span>
            </div>
        </div>

        <section class="container details">
            <div class="details-hero">
                <img id="mainImage" src="<?= e(image_url($images[0])) ?>" alt="<?= e($place['name']) ?>">
                <div class="overlay">
                    <div>
                        <span class="category <?= $catSlug ?>"><?= category_icon($place['category']) ?> <?= e($place['category']) ?></span>
                        <h1><?= e($place['name']) ?></h1>
                    </div>
                </div>
            </div>

            <?php if (count($images) > 1): ?>
            <div class="gallery-thumbs">
                <?php foreach ($images as $i => $img): ?>
                    <img src="<?= e(image_url($img)) ?>" class="<?= $i === 0 ? 'active' : '' ?>" alt="صورة <?= $i + 1 ?> من <?= e($place['name']) ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="place-details">
                    <h3>📍 الموقع</h3>
                    <p><?= e($place['location']) ?></p>
                </div>
                <div class="place-details">
                    <h3>⭐ التقييم</h3>
                    <p class="rating">★★★★★ (4.5/5)</p>
                    <?php if (!empty($place['phone'])): ?>
                        <p class="phone">📞 <a href="tel:<?= e(preg_replace('/[^\d+]/', '', $place['phone'])) ?>" dir="ltr"><?= e($place['phone']) ?></a></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="place-details">
                <h3>🗺️ الموقع على الخريطة</h3>
                <div class="map-embed">
                    <iframe src="<?= e($mapsEmbed) ?>" title="خريطة <?= e($place['name']) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
                <a class="btn map-btn" href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener">🗺️ فتح في خرائط قوقل</a>
            </div>

            <div class="place-details">
                <h3>📝 الوصف</h3>
                <p><?= nl2br(e($place['description'])) ?></p>
            </div>

            <div class="place-details">
                <h3>✨ المميزات</h3>
                <ul class="features">
                    <li>✓ مكان آمن وموثوق</li>
                    <li>✓ خدمات سياحية متميزة</li>
                    <li>✓ إطلالات طبيعية خلابة</li>
                    <li>✓ مرافق حديثة</li>
                </ul>
            </div>

            <div class="btn-row center">
                <a href="contact.php" class="btn">تواصل معنا 📧</a>
                <a href="places.php" class="btn btn-secondary">العودة للأماكن ←</a>
            </div>

            <?php if ($similar): ?>
            <div class="section-head">
                <div><h2>أماكن مشابهة</h2></div>
            </div>
            <div class="scroll-row">
                <?php foreach ($similar as $place): ?>
                    <?php require __DIR__ . '/includes/place-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
