# المعمارية وهيكل المشروع

## نظرة عامة

التطبيق Laravel أحادي النشر (modular monolith): توجد نواة مشتركة في `app/`، ووحدات نطاق العمل في `Modules/`. تشترك الوحدات في قاعدة بيانات التطبيق وآلية تسجيل الدخول والصلاحيات، وتضيف كل وحدة خدماتها ومساراتها وواجهاتها وترحيلاتها.

```text
app/                 النواة المشتركة: المصادقة، الخدمات، المستودعات، المساعدات
Modules/             وحدات نطاق العمل
routes/              مسارات التطبيق الأساسية
database/            ترحيلات وبذور ومصانع مشتركة
resources/           واجهات Blade وأصول المصدر
public/              نقطة الدخول والأصول المنشورة وملفات Vite المبنية
config/              إعدادات Laravel والحزم
android/, ios/       أغلفة Capacitor للهاتف
mobile-app-shell/    ملفات الويب التي يشير إليها إعداد Capacitor
storage/             سجلات وملفات تشغيلية؛ لا تحفظ الأسرار هنا في المستودع
```

## طلب الويب

1. يدخل الطلب عبر `public/index.php` إلى Laravel.
2. تُسجّل الخدمات الأساسية من `bootstrap/providers.php`، بينما تُحمّل وحدات `nwidart/laravel-modules` مزودي الخدمات المعرفة في ملفات `Modules/*/module.json`.
3. المسارات العامة المشتركة موجودة في `routes/web.php`. مسارات الوظائف تكون عادة في `Modules/<Module>/routes/`.
4. تمر صفحات التطبيق الداخلية عبر المصادقة والصلاحيات، ثم تصل إلى Controllers وخدمات ومستودعات ونماذج الوحدة.
5. تعرض واجهات Blade من `resources/views` أو من مجلد `resources/views` الخاص بالوحدة، وتُبنى الأصول الأمامية عبر Vite.

## تنظيم الوحدة

تتبع معظم الوحدات شكل Laravel Modules، وقد تختلف الملفات بحسب حجم الوحدة:

```text
Modules/<Name>/
  app/                 Controllers, Models, Services, Repositories, Providers
  database/            migrations, seeders, factories
  resources/           views, assets, lang
  routes/              web.php و/أو api.php
  module.json          اسم الوحدة ومزود الخدمة
  composer.json        اعتماديات/خرائط PSR-4 للوحدة عند الحاجة
```

ليست كل الوحدات متطابقة؛ تحقّق من محتوى الوحدة قبل افتراض وجود مجلد بعينه. التفعيل الحالي موثق في `modules_statuses.json`.

## النواة المشتركة

- `app/Http/Controllers/Auth` و`app/Services/AuthService.php`: نقاط المصادقة المشتركة.
- `app/Providers/RepositoryServiceProvider.php`: ربط واجهات المستودعات بالتطبيقات.
- `app/Providers/MenuServiceProvider.php` و`app/Helpers/MenuHelper.php`: تكامل قوائم التنقل.
- `app/Http/Middleware/SetLocale.php` و`lang/`: اللغة والترجمة.
- `app/Services/GlobalSearchService.php`: البحث العام عبر واجهة التطبيق.
- `Modules/AccessControl`: إدارة الأدوار والصلاحيات؛ مبني كذلك على Spatie Permission.
- `Modules/AuditLog`: سجل الأنشطة والتدقيق.

## الواجهة وتطبيق الهاتف

الويب يستخدم Blade وBootstrap وأصولًا مبنية بواسطة Vite. ملفات المصدر في `resources/` وملفات البناء في `public/build/`. لا تعدّل الملفات المولدة يدويًا؛ عدّل المصدر ثم أعد البناء.

Capacitor مهيأ في `capacitor.config.json` ويستخدم عنوان البوابة المستضافة ضمن `server.url`. إعداد النشر الفعلي يعتمد على ذلك العنوان وإعدادات Android/iOS.

## إضافة أو تعديل موديول

1. افحص مزود الخدمة في `module.json` وحالة الوحدة في `modules_statuses.json`.
2. ضع منطق النطاق داخل الوحدة، وأبقِ الوظائف المشتركة في `app/` عند وجود أكثر من مستهلك.
3. اربط الصلاحيات والمسارات بوضوح، واستخدم Form Requests للتحقق من المدخلات.
4. أضف migration وseeder عند الحاجة، وتأكد من ترتيب الاعتماديات بين الوحدات.
5. حدّث هذه الوثائق إذا أضفت وظيفة أو تكاملًا يؤثر في التشغيل أو هيكل المشروع.
