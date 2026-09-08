<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;


class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $name;
    protected $type;
    public $tries = 2;
    public $timeout = 3700;
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct($path, $name, $type)
    {
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $outputDir = public_path($this->path . $this->name . "/");
        $inputFile = public_path($this->path . $this->name . $this->type);

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0775, true);
        }

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => config('media.ffmpeg'),
            'ffprobe.binaries' => config('media.ffprobe'),
            'timeout'          => config('media.timeout'),
            'ffmpeg.threads'   => config('media.threads'),
        ]);

        $video = $ffmpeg->open($inputFile);

        $qualities = [
            '1080p' => [1920, 1080, 5000],
            '720p'  => [1280, 720, 2800],
            '480p'  => [854, 480, 1400],
            '360p'  => [640, 360, 800],
            '240p'  => [426, 240, 500],
            '144p'  => [256, 144, 300],
        ];

        $masterPlaylist = "#EXTM3U\n";

        foreach ($qualities as $quality => $settings) {
            [$width, $height, $bitrate] = $settings;

            $format = new X264('aac', 'libx264');
            $format->setKiloBitrate($bitrate);
            $format->setAudioKiloBitrate(128);

            $outputPath = $outputDir . $this->name . '_' . $quality . ".m3u8";

            $format->setAdditionalParameters([
                '-hls_time', '10',
                '-hls_playlist_type', 'vod',
                '-hls_segment_filename', $outputDir . $this->name . "_{$quality}_%03d.ts",
            ]);

            $video->filters()->resize(new Dimension($width, $height))->synchronize();
            $video->save($format, $outputPath);

            $masterPlaylist .= "#EXT-X-STREAM-INF:BANDWIDTH=" . ($bitrate * 1000) . ",RESOLUTION={$width}x{$height}\n";
            $masterPlaylist .= $this->name . '_' . $quality . ".m3u8\n";
        }

        file_put_contents($outputDir . "playlist.m3u8", $masterPlaylist);
    }
}
