# Social Network

تطبيق شبكة اجتماعية مبني بـ Laravel 11 وPHP 8.2 وjQuery. يوفر موجز منشورات قريبًا من تجربة فيسبوك، ملفات شخصية، تعليقات وردود، سبعة أنواع من التفاعلات، حفظ المنشورات، البحث، الصفحات والمجموعات، ودردشة فورية مع مكالمات صوت وفيديو.

## المتطلبات

- PHP 8.2 أو أحدث مع SQLite أو MySQL
- Composer
- FFmpeg وFFprobe لمعالجة الفيديو
- Node.js اختياري عند تعديل ملفات الواجهة المصدرية

## التشغيل السريع

```bash
composer install
cp .env.example .env
php artisan key:generate
```

للتجربة السريعة استخدم SQLite، ثم أنشئ الملف `database/database.sqlite` وعدّل `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

بعدها شغّل:

```bash
php artisan migrate --seed
php artisan serve
```

افتح `http://127.0.0.1:8000`. بيانات الحساب التجريبي:

- البريد: `demo@example.com`
- كلمة المرور: `Demo@12345`

ينشئ Seeder مستخدمين إضافيين بنفس كلمة المرور حتى يمكن تجربة الدردشة والتفاعلات فورًا.

## الدردشة والمكالمات

ثبت مكتبات خادم WebSocket وشغّله في نافذة مستقلة:

```bash
cd websocket-server
composer install
php server2.php
```

الإعداد الافتراضي هو `ws://127.0.0.1:8081`. يمكن تغييره عبر `WEBSOCKET_URL` و`WEBSOCKET_HOST` و`WEBSOCKET_PORT` في `.env`.

## معالجة الفيديو

ضع مساري FFmpeg وFFprobe في `.env`، أو استخدم الملفات المرفقة في مجلد `bin`:

```env
FFMPEG_BINARIES=/absolute/path/to/bin/ffmpeg.exe
FFPROBE_BINARIES=/absolute/path/to/bin/ffprobe.exe
```

تشغيل عامل الطابور مطلوب عند استخدام `QUEUE_CONNECTION=database` أو أي مشغل غير `sync`:

```bash
php artisan queue:work --tries=2 --timeout=3700
```

## الاختبارات

```bash
php artisan test
```

تغطي الاختبارات الحالية تسجيل الدخول إلى API، إنشاء المنشورات، الحماية بالتوكن، الحفظ، التفاعلات، حماية الصفحات وتبديل اللغة. GitHub Actions يشغّلها تلقائيًا عند كل رفع أو Pull Request.

## API والنشر

- تفاصيل API وأمثلة الطلبات في [API.md](API.md).
- خطوات تجهيز بيئة الإنتاج في [DEPLOYMENT.md](DEPLOYMENT.md).

لا ترفع ملف `.env` أو قاعدة البيانات المحلية أو مجلدات `vendor` إلى Git. استخدم HTTPS و`wss://` في الإنتاج، واضبط `APP_DEBUG=false`.
