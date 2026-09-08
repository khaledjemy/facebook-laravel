<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 3700;
    public $backoff = 30;

    public function __construct(
        protected string $inputRelativePath,
        protected string $outputRelativeDirectory,
        protected ?string $modelClass = null,
        protected ?int $modelId = null,
        protected ?string $modelAttribute = null,
    ) {}

    public function handle(): void
    {
        $input = public_path($this->inputRelativePath);
        $outputDirectory = public_path($this->outputRelativeDirectory);

        if (!is_file($input)) {
            throw new \RuntimeException("Video source does not exist: {$input}");
        }
        if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0775, true) && !is_dir($outputDirectory)) {
            throw new \RuntimeException("Could not create video output directory: {$outputDirectory}");
        }

        [$sourceWidth, $sourceHeight] = $this->sourceDimensions($input);
        $qualities = [1080 => 5000, 720 => 2800, 480 => 1400, 360 => 800, 240 => 500];
        $qualities = array_filter($qualities, fn (int $bitrate, int $height) => $height <= $sourceHeight, ARRAY_FILTER_USE_BOTH);
        if ($qualities === []) {
            $qualities = [min(240, max(144, $sourceHeight)) => 400];
        }

        $master = "#EXTM3U\n#EXT-X-VERSION:3\n";
        foreach ($qualities as $height => $bitrate) {
            $width = max(2, (int) (floor(($sourceWidth * $height / $sourceHeight) / 2) * 2));
            $playlistName = "{$height}p.m3u8";
            $playlist = $outputDirectory.DIRECTORY_SEPARATOR.$playlistName;
            $process = new Process([
                config('media.ffmpeg'), '-y', '-i', $input,
                '-map', '0:v:0', '-map', '0:a?', '-vf', "scale=-2:{$height}",
                '-c:v', 'libx264', '-preset', 'veryfast', '-profile:v', 'main', '-pix_fmt', 'yuv420p',
                '-b:v', "{$bitrate}k", '-maxrate', (string) round($bitrate * 1.1).'k', '-bufsize', ($bitrate * 2).'k',
                '-c:a', 'aac', '-b:a', '128k', '-ar', '48000',
                '-force_key_frames', 'expr:gte(t,n_forced*6)', '-hls_time', '6', '-hls_playlist_type', 'vod',
                '-hls_segment_filename', $outputDirectory.DIRECTORY_SEPARATOR."{$height}p_%03d.ts", $playlist,
            ]);
            $process->setTimeout(config('media.timeout'));
            $process->mustRun();
            $master .= '#EXT-X-STREAM-INF:BANDWIDTH='.(($bitrate + 128) * 1000)
                .",RESOLUTION={$width}x{$height},NAME=\"{$height}p\"\n{$playlistName}\n";
        }

        file_put_contents($outputDirectory.DIRECTORY_SEPARATOR.'playlist.m3u8', $master);
        $processedPath = trim(str_replace('\\', '/', $this->outputRelativeDirectory), '/').'/playlist.m3u8';
        if ($this->modelClass && $this->modelId && $this->modelAttribute) {
            $model = ($this->modelClass)::find($this->modelId);
            if ($model) {
                $model->forceFill([$this->modelAttribute => $processedPath])->save();
            }
        }
        if (is_file($input)) {
            unlink($input);
        }
    }

    private function sourceDimensions(string $input): array
    {
        $process = new Process([
            config('media.ffprobe'), '-v', 'error', '-select_streams', 'v:0',
            '-show_entries', 'stream=width,height', '-of', 'csv=p=0:s=x', $input,
        ]);
        $process->setTimeout(60);
        $process->mustRun();
        $dimensions = array_map('intval', explode('x', trim($process->getOutput())));

        return [max(1, $dimensions[0] ?? 1), max(1, $dimensions[1] ?? 1)];
    }
}
