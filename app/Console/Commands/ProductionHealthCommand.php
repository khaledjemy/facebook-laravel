<?php

namespace App\Console\Commands;

use App\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class ProductionHealthCommand extends Command
{
    protected $signature = 'app:health';

    protected $description = 'Check production dependencies for the social network';

    public function handle(): int
    {
        $checks = [
            'Production environment' => fn () => app()->environment('production'),
            'Debug disabled' => fn () => !config('app.debug'),
            'Application key configured' => fn () => filled(config('app.key')),
            'HTTPS application URL' => fn () => str_starts_with((string) config('app.url'), 'https://'),
            'Secure session cookie' => fn () => (bool) config('session.secure'),
            'Database' => fn () => DB::select('select 1') !== [],
            'Storage directory writable' => fn () => is_writable(storage_path()),
            'Bootstrap cache writable' => fn () => is_writable(base_path('bootstrap/cache')),
            'Public storage link' => fn () => file_exists(public_path('storage')),
            'PHP upload limits match site setting' => fn () => $this->uploadLimitsSupportConfiguredMaximum(),
            'Mail transport configured' => fn () => $this->mailTransportConfigured(),
            'FFmpeg' => fn () => $this->binaryWorks((string) config('media.ffmpeg')),
            'FFprobe' => fn () => $this->binaryWorks((string) config('media.ffprobe')),
            'WebSocket secret' => fn () => filled(config('services.websocket_secret')),
            'Secure WebSocket URL' => fn () => str_starts_with((string) config('services.websocket_url'), 'wss://'),
            'Background queue' => fn () => config('queue.default') !== 'sync',
        ];

        $failed = false;
        foreach ($checks as $label => $check) {
            try {
                $ok = (bool) $check();
            } catch (\Throwable $exception) {
                $ok = false;
            }

            $this->line(($ok ? '<fg=green>PASS</>' : '<fg=red>FAIL</>')."  {$label}");
            $failed = $failed || !$ok;
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function binaryWorks(string $binary): bool
    {
        if ($binary === '') {
            return false;
        }

        $process = new Process([$binary, '-version']);
        $process->setTimeout(10);
        $process->run();

        return $process->isSuccessful();
    }

    private function mailTransportConfigured(?string $mailer = null, int $depth = 0): bool
    {
        if ($depth > 3) {
            return false;
        }

        $mailer ??= (string) config('mail.default');
        $fromAddress = (string) config('mail.from.address', '');
        if (!filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $configuration = config('mail.mailers.'.$mailer);
        if (!is_array($configuration)) {
            return false;
        }

        return match ($configuration['transport'] ?? $mailer) {
            'smtp' => filled($configuration['host'] ?? null)
                && !in_array(strtolower((string) $configuration['host']), ['localhost', '127.0.0.1', 'example.com'], true)
                && (int) ($configuration['port'] ?? 0) > 0
                && (int) ($configuration['port'] ?? 0) <= 65535
                && filled($configuration['username'] ?? null)
                && filled($configuration['password'] ?? null),
            'sendmail' => $this->sendmailConfigured($configuration),
            'failover' => collect($configuration['mailers'] ?? [])
                ->contains(fn ($fallback) => $this->mailTransportConfigured((string) $fallback, $depth + 1)),
            default => false,
        };
    }

    private function sendmailConfigured(array $configuration): bool
    {
        $path = trim((string) ($configuration['path'] ?? ''));
        if ($path === '') {
            return false;
        }

        $binary = strtok($path, ' ');

        return $binary !== false && is_executable($binary);
    }

    private function uploadLimitsSupportConfiguredMaximum(): bool
    {
        $maxUploadMb = (int) SiteSetting::getValue('max_upload_mb', 200);
        if ($maxUploadMb < 1) {
            return false;
        }

        $requiredBytes = $maxUploadMb * 1024 * 1024;

        return $this->iniSizeBytes(ini_get('upload_max_filesize')) >= $requiredBytes
            && $this->iniSizeBytes(ini_get('post_max_size'), true) > $requiredBytes;
    }

    private function iniSizeBytes(string|false $value, bool $zeroMeansUnlimited = false): int
    {
        $value = trim((string) $value);
        if ($zeroMeansUnlimited && (float) $value === 0.0) {
            return PHP_INT_MAX;
        }

        if (!preg_match('/^([0-9]+(?:\.[0-9]+)?)\s*([kmgt]?)b?$/i', $value, $matches)) {
            return 0;
        }

        $unit = strtolower($matches[2]);
        $power = match ($unit) {
            'k' => 1,
            'm' => 2,
            'g' => 3,
            't' => 4,
            default => 0,
        };

        return (int) ((float) $matches[1] * (1024 ** $power));
    }
}
