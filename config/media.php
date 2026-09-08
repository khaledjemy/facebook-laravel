<?php
return [
    'ffmpeg' => env('FFMPEG_BINARIES', base_path('bin/ffmpeg.exe')),
    'ffprobe' => env('FFPROBE_BINARIES', base_path('bin/ffprobe.exe')),
    'timeout' => (int) env('FFMPEG_TIMEOUT', 3600),
    'threads' => (int) env('FFMPEG_THREADS', 4),
];
