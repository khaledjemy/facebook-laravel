<?php

namespace App\Services;

use App\Jobs\ProcessVideoJob;
use App\Photo;
use App\Video;
use Illuminate\Http\UploadedFile;

class MediaUploadService
{
    protected array $allowedTypes = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/avi', 'video/x-msvideo', 'video/mkv', 'video/x-matroska',
        'video/mov', 'video/quicktime', 'video/wmv', 'video/flv', 'video/webm',
        'video/mpeg', 'video/3gpp',
    ];

    protected int $maxSizeBytes = 209715200; // 200 MB

    public function processUploads(array|UploadedFile $files): array
    {
        $fileList = is_array($files) ? $files : [$files];
        $photoFiles = [];
        $videoFiles = [];

        foreach ($fileList as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            if (!in_array($file->getMimeType(), $this->allowedTypes, true)) {
                return [
                    'status' => 'error',
                    'error' => 'failed',
                    'details' => $file->getClientOriginalName() . ' has an unsupported file type.',
                ];
            }

            if ($file->getSize() > $this->maxSizeBytes) {
                return [
                    'status' => 'error',
                    'error' => 'failed',
                    'details' => $file->getClientOriginalName() . ' exceeds the maximum allowed size of 200 MB.',
                ];
            }

            if (str_starts_with((string) $file->getMimeType(), 'video/')) {
                $videoFiles[] = $file;
            } else {
                $photoFiles[] = $file;
            }
        }

        $results = [];
        $types = [];

        if (!empty($videoFiles)) {
            $results['video'] = $this->uploadVideos($videoFiles);
            $types[] = ['video'];
        }

        if (!empty($photoFiles)) {
            $results['photo'] = $this->uploadImages($photoFiles);
            $types[] = ['img'];
        }

        return [
            'status' => 'ok',
            'details' => $results,
            'type' => $types,
        ];
    }

    public function uploadImages(array $photos): string
    {
        $encoded = [];
        $userId = auth()->id();

        foreach ($photos as $key => $file) {
            $extension = $this->safeExtension($file);
            $dotType = '.' . $extension;

            $photo = Photo::create([
                'user_id' => $userId,
                'path' => 'images/users/' . $userId . '/',
                'state' => 1,
                'album_id' => 0,
                'type' => $dotType,
            ]);

            if ($photo) {
                $directory = public_path($photo->path);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $photo->id . $dotType);
            }

            $encoded[$key] = [$photo->id];
        }

        return json_encode($encoded, JSON_FORCE_OBJECT);
    }

    public function uploadVideos(array $videos): string
    {
        $encoded = [];
        $userId = auth()->id();

        foreach ($videos as $key => $file) {
            $dotType = '.' . $this->safeExtension($file);

            $video = Video::create([
                'user_id' => $userId,
                'path' => 'video/users/' . $userId . '/',
                'state' => 1,
                'album_id' => 0,
                'type' => $dotType,
            ]);

            if ($video) {
                $directory = public_path($video->path);
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $file->move($directory, $video->id . $dotType);
            }

            $encoded[$key] = [$video->id];

            ProcessVideoJob::dispatch(
                $video->path . $video->id . $dotType,
                $video->path . $video->id,
            );
        }

        return json_encode($encoded, JSON_FORCE_OBJECT);
    }

    public function safeExtension(UploadedFile $file): string
    {
        return match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/quicktime', 'video/mov' => 'mov',
            'video/avi', 'video/x-msvideo' => 'avi',
            'video/mkv', 'video/x-matroska' => 'mkv',
            'video/wmv' => 'wmv',
            'video/flv' => 'flv',
            'video/mpeg' => 'mpeg',
            'video/3gpp' => '3gp',
            default => throw new \InvalidArgumentException('Unsupported media type.'),
        };
    }
}
