# Production checklist

1. Configure `.env` with `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, database credentials and a strong `WEBSOCKET_SECRET`.
2. Set `QUEUE_CONNECTION=database`, `VIDEO_QUEUE=videos`, and `QUEUE_RETRY_AFTER=3900`.
3. Set absolute `FFMPEG_BINARIES` and `FFPROBE_BINARIES` paths.
4. Use `wss://` for `WEBSOCKET_URL` behind an HTTPS reverse proxy.
5. Run `php artisan migrate --force`, `php artisan storage:link`, and `php artisan optimize`.
6. Install the Supervisor examples from `deploy/supervisor`, adjusting the project path and Linux user.
7. Run `php artisan app:health`. Every required check should show `PASS`.
8. Restart workers after each deployment with `php artisan queue:restart`.

The video worker timeout is intentionally shorter than `QUEUE_RETRY_AFTER`, preventing the same FFmpeg job from running twice.
