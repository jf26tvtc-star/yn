<?php
// بطاقة مكان واحدة. يتطلب المتغير $place (id, name, location, category, image) و $showExcerpt اختياريًا مع description.
$slug = category_slug($place['category']);
?>
<article class="place-card <?= $slug ?>">
    <div class="img-wrap">
        <img src="<?= e(image_url($place['image'])) ?>" alt="<?= e($place['name']) ?>" loading="lazy">
    </div>
    <div class="place-card-content">
        <span class="category <?= $slug ?>"><?= category_icon($place['category']) ?> <?= e($place['category']) ?></span>
        <h3><?= e($place['name']) ?></h3>
        <p class="location">📍 <?= e($place['location']) ?></p>
        <?php if (!empty($showExcerpt)): ?>
            <p><?= e(excerpt($place['description'])) ?></p>
        <?php endif; ?>
        <a href="details.php?id=<?= (int)$place['id'] ?>" class="btn btn-sm">عرض التفاصيل</a>
    </div>
</article>
