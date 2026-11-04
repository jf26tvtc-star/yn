# روح ينبع — دليل سياحي لمدينة ينبع

موقع PHP + MySQL يعمل على XAMPP: دليل للأماكن السياحية في ينبع مع لوحة تحكم لإدارة الأماكن والصور والتصنيفات ورسائل الزوار.

## التشغيل

1. ضع مجلد المشروع في `C:\xampp\htdocs\` باسم `yanbu-tourism`.
2. شغّل **Apache** و **MySQL** من XAMPP (PHP 8.1 فأعلى).
3. من phpMyAdmin استورد `database/schema.sql` (تثبيت جديد — يحذف جداول المشروع ويعيد إنشاءها).
   - لقاعدة موجودة فيها بيانات: استورد `database/upgrade.sql` بدلًا منه (لا يحذف شيئًا).
4. افتح الموقع: <http://localhost/yanbu-tourism/>
5. لوحة التحكم: <http://localhost/yanbu-tourism/admin/>
   - اسم المستخدم: `admin`
   - كلمة المرور: `admin123`

إعدادات الاتصال الافتراضية: `localhost` / `root` / بدون كلمة مرور / قاعدة `yanbu_tourism`.
يمكن تغييرها عبر متغيرات البيئة `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASSWORD`, `DB_NAME` (انظر `includes/database.php`).

## هيكل المشروع

```
yanbu-tourism/
├── index.php            الصفحة الرئيسية
├── places.php           كل الأماكن (بحث + تصفية + صفحات)
├── details.php          تفاصيل مكان (معرض صور + خريطة + أماكن مشابهة)
├── categories.php       التصنيفات
├── destination.php      مناطق ينبع (البحر / النخل / الصناعية)
├── trip.php             خطط رحلتك
├── about.php            من نحن
├── contact.php          تواصل معنا (تُحفظ الرسائل في قاعدة البيانات)
├── 404.php              صفحة غير موجودة
├── admin/               لوحة التحكم
│   ├── login.php / logout.php
│   ├── dashboard.php    نظرة عامة
│   ├── places.php, add_place.php, edit_place.php, delete_place.php
│   ├── set_main_image.php, delete_image.php
│   ├── categories.php, edit_category.php, delete_category.php
│   ├── messages.php     رسائل الزوار
│   └── partials/        قالب اللوحة المشترك (القائمة الجانبية، التذييل، نموذج المكان)
├── includes/            ملفات PHP المشتركة (لا تُفتح من المتصفح)
│   ├── bootstrap.php    يُستدعى أول كل صفحة
│   ├── database.php     الاتصال بقاعدة البيانات
│   ├── functions.php    الدوال المشتركة (الصور، التصنيفات، الحماية...)
│   ├── header.php / footer.php   رأس وتذييل الصفحات العامة
│   └── place-card.php   بطاقة المكان
├── assets/              css + js + الصورة الافتراضية
├── uploads/             صور الأماكن المرفوعة من لوحة التحكم
└── database/            schema.sql (تثبيت جديد) + upgrade.sql (ترقية)
```

## قاعدة البيانات

- `categories` (1) ←→ (N) `places` (1) ←→ (N) `images`
- `users` حسابات الإدارة، و `messages` رسائل الزوار.
- لا يمكن حذف تصنيف مرتبط بأماكن، وحذف المكان يحذف صوره (السجلات والملفات).

## الحماية

- كل الاستعلامات prepared statements، وكل المخرجات مُهرّبة (htmlspecialchars).
- كل نماذج POST محمية برمز CSRF، والحذف يتم عبر POST فقط.
- الدخول يُقفل دقيقة بعد 5 محاولات خاطئة.
- الصور المرفوعة يُتحقق منها (JPG/PNG/WEBP حتى 5MB، 10 صور كحد أقصى لكل مكان)، ومجلد `uploads` لا يشغّل أي سكربت.
- روابط خرائط قوقل تقبل http/https فقط.

## ملاحظات

- "التقييم" و"المميزات" في صفحة التفاصيل نصوص ثابتة وليست من قاعدة البيانات.
- يُنصح بتغيير كلمة مرور `admin` قبل نشر الموقع على الإنترنت.

## حقوق الصور

- `uploads/place_6ad7128249b062.66658225.jpg` (مهرجان الزهور والحدائق بينبع): تصوير Ahmed Abdulbasit،
  المصدر: <https://commons.wikimedia.org/wiki/File:Yanbu_flower_festival.jpg> — رخصة CC BY-SA 4.0.
