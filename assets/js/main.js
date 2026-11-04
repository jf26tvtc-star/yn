// معرض صور صفحة التفاصيل: الضغط على صورة مصغّرة يعرضها كصورة رئيسية
document.addEventListener('DOMContentLoaded', function () {
    var main = document.getElementById('mainImage');
    var thumbs = document.querySelectorAll('.gallery-thumbs img');
    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            main.src = thumb.src;
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');
        });
    });
});
