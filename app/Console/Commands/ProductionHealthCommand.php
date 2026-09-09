<?php

namespace App\Console\Commands;

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
            'Database' => fn () => DB::select('select 1') !== [],
            'Public directory writable' => fn () => is_writable(public_path()),
            'Storage directory writable' => fn () => is_writable(storage_path()),
            'FFmpeg' => fn () => $this->binaryWorks((string) config('media.ffmpeg')),
            'FFprobe' => fn () => $this->binaryWorks((string) config('media.ffprobe')),
            'WebSocket secret' => fn () => filled(config('services.websocket_secret')),
            'WebSocket URL' => fn () => filled(config('services.websocket_url')),
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

        if (config('app.debug')) {
            $this->warn('APP_DEBUG is enabled; disable it in production.');
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
}
