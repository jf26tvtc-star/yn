<?php
// نموذج بيانات المكان المشترك بين صفحتي الإضافة والتعديل.
// يتطلب: $values (قيم الحقول)، $categories، $submitLabel، $imagesCount (عدد الصور الحالية)
?>
<form class="dash-panel panel-form" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="panel-grid">
        <label>اسم المكان *
            <input name="name" required maxlength="150" value="<?= e($values['name']) ?>">
        </label>
        <label>التصنيف *
            <select name="category_id" required>
                <option value="">اختر التصنيف</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>"<?= (int)$values['category_id'] === (int)$cat['id'] ? ' selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>الموقع *
            <input name="location" required maxlength="200" value="<?= e($values['location']) ?>" placeholder="مثال: ينبع البحر - الكورنيش">
        </label>
        <label>رقم الهاتف (اختياري)
            <input name="phone" type="tel" maxlength="30" dir="ltr" value="<?= e($values['phone']) ?>">
        </label>
    </div>
    <label>رابط خرائط قوقل (اختياري — يُولَّد تلقائيًا إن تُرك فارغًا)
        <input name="google_maps_url" type="url" maxlength="500" dir="ltr" value="<?= e($values['google_maps_url']) ?>" placeholder="https://maps.app.goo.gl/...">
    </label>
    <label>وصف المكان *
        <textarea name="description" required rows="7"><?= e($values['description']) ?></textarea>
    </label>
    <label><?= $imagesCount ? 'إضافة صور جديدة (اختياري)' : 'صور المكان (أول صورة تصبح الصورة الرئيسية)' ?>
        <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
    </label>
    <p class="panel-muted">JPG أو PNG أو WEBP، حتى 5MB للصورة، وبحد أقصى <?= MAX_PLACE_IMAGES ?> صور لكل مكان<?= $imagesCount ? ' (لديك ' . $imagesCount . ' حاليًا)' : '' ?>.</p>
    <div class="panel-actions">
        <button class="dash-button dash-button-primary" type="submit"><?= e($submitLabel) ?></button>
        <a class="dash-button panel-secondary" href="places.php">إلغاء</a>
    </div>
</form>
