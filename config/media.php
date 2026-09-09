<?php
return [
    'queue' => env('VIDEO_QUEUE', 'videos'),
    'ffmpeg' => env('FFMPEG_BINARIES', base_path('bin/ffmpeg.exe')),
    'ffprobe' => env('FFPROBE_BINARIES', base_path('bin/ffprobe.exe')),
    'timeout' => (int) env('FFMPEG_TIMEOUT', 3600),
    'threads' => (int) env('FFMPEG_THREADS', 4),
    'sync_initial_quality' => filter_var(env('VIDEO_SYNC_INITIAL_QUALITY', true), FILTER_VALIDATE_BOOL),
];
