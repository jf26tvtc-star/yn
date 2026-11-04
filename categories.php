<?php
require __DIR__ . '/includes/bootstrap.php';
$conn = db();

$categories = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM places p WHERE p.category_id = c.id) AS places_count,
    COALESCE((SELECT i.image_path FROM places p2 JOIN images i ON i.place_id = p2.id WHERE p2.category_id = c.id ORDER BY i.is_main DESC, i.id ASC LIMIT 1), '') AS cover
    FROM categories c ORDER BY c.id")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'التصنيفات السياحية';
require __DIR__ . '/includes/header.php';
?>
        <section class="hero hero-small">
            <div class="container">
                <h1>التصنيفات السياحية</h1>
                <p>تصفّح وجهات ينبع حسب نوعها: بحرية، تاريخية، طبيعية، وترفيهية</p>
            </div>
        </section>

        <section class="container">
            <?php if ($categories): ?>
                <div class="category-grid">
                    <?php foreach ($categories as $c): ?>
                        <a class="category-card <?= category_slug($c['name']) ?>" href="places.php?category=<?= urlencode($c['name']) ?>">
                            <img src="<?= e(image_url($c['cover'])) ?>" alt="<?= e($c['name']) ?>" loading="lazy">
                            <div class="cc-body">
                                <h3><?= category_icon($c['name']) ?> <?= e($c['name']) ?></h3>
                                <p><?= e($c['description']) ?></p>
                                <p class="cc-count"><?= (int)$c['places_count'] ?> مكان مسجّل</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="empty-note">لا توجد تصنيفات بعد.</p>
            <?php endif; ?>
        </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
