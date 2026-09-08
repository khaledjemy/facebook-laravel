# تجهيز وتشغيل التطبيق للبيع

## متطلبات الخادم

- PHP 8.2 أو أحدث مع الامتدادات `pdo`, `mbstring`, `fileinfo`, `openssl`.
- Composer 2.
- MySQL 8 أو SQLite.
- Node.js 18 أو أحدث لبناء ملفات الواجهة.

## التثبيت

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force
npm ci
npm run production
php artisan storage:link
php artisan optimize
```

قبل النشر اضبط بيانات قاعدة البيانات والبريد، واجعل `APP_DEBUG=false` و`APP_ENV=production` وغيّر كلمات مرور الحسابات التجريبية أو احذفها.

اضبط `FFMPEG_BINARIES` و`FFPROBE_BINARIES` بالمسارات المطلقة على خادم Linux. شغّل `php artisan queue:work --tries=2 --timeout=3700` لمعالجة جودات الفيديو في الخلفية، ويفضل جعل `QUEUE_CONNECTION=database` في الإنتاج.

يجب أن يشير Document Root في الخادم إلى مجلد `public`، وأن تكون مجلدات `storage` و`bootstrap/cache` قابلة للكتابة.
