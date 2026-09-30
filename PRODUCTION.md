# Production checklist

1. Run PHP 8.3 or newer. Configure `.env` with `APP_ENV=production`, `APP_DEBUG=false`, an HTTPS `APP_URL`, `SESSION_SECURE_COOKIE=true`, database credentials, SMTP mail, and a strong `WEBSOCKET_SECRET`.
2. Set `QUEUE_CONNECTION=database`, `VIDEO_QUEUE=videos`, and `QUEUE_RETRY_AFTER=3900`.
3. Set absolute `FFMPEG_BINARIES` and `FFPROBE_BINARIES` paths.
4. Use `wss://` for `WEBSOCKET_URL` behind an HTTPS reverse proxy.
5. For a new production database, provide `INITIAL_ADMIN_NAME`, `INITIAL_ADMIN_EMAIL`, and a 16-character-minimum `INITIAL_ADMIN_PASSWORD` through a secret manager. Run the seeder once; it creates only this administrator in production. Remove the bootstrap password secret after creation. Never use the local demo credentials on a customer site.
6. New registrations must verify their email before using authenticated site features. Existing accounts are marked verified during the upgrade migration so current customers are not locked out. Confirm verification and password-reset mail delivery through the production SMTP provider.
7. Run `php artisan migrate --force`, `php artisan db:seed --force`, `php artisan storage:link`, and `php artisan optimize`.
8. Install the Supervisor examples from `deploy/supervisor`, adjusting the project path and Linux user.
9. Set PHP `upload_max_filesize` to at least the upload limit configured in the admin panel and `post_max_size` to a larger value. Run `php artisan app:health`; it checks this alongside production mode, debug, HTTPS/WSS, secure session cookies, sender address and authenticated SMTP (or working sendmail), storage, media tools, and queue configuration. Every check should show `PASS`.
10. Restart workers after each deployment with `php artisan queue:restart`.
11. Create automated, off-server backups for both the database and `storage/app/public`, and test restoring them before accepting production data.

The example environment intentionally defaults to local development and log-only mail. Never copy it unchanged to a customer server; enter real SMTP credentials and a verified sender address in the production secret manager. The admin upload limit is capped at 500 MB, so configure PHP and the reverse proxy/web server to accept the selected limit plus multipart overhead.

The video worker timeout is intentionally shorter than `QUEUE_RETRY_AFTER`, preventing the same FFmpeg job from running twice.
