<?php

namespace App\Services;

use App\Jobs\ProcessVideoJob;
use App\photo as Photo;
use App\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    protected array $allowedTypes = [
        'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/avi', 'video/x-msvideo', 'video/mkv', 'video/x-matroska',
        'video/mov', 'video/quicktime', 'video/wmv', 'video/flv', 'video/webm',
        'video/mpeg', 'video/3gpp',
    ];

    protected int $maxSizeBytes = 209715200; // 200 MB

    public function processUploads(array|UploadedFile $files, array $videoMetadata = []): array
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
            $results['video'] = $this->uploadVideos($videoFiles, $videoMetadata);
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
                'path' => 'private/media/images/users/' . $userId . '/',
                'state' => 1,
                'album_id' => 0,
                'type' => $dotType,
            ]);

            if ($photo) {
                Storage::disk('local')->putFileAs($photo->path, $file, $photo->id . $dotType);
            }

            $encoded[$key] = [$photo->id];
        }

        return json_encode($encoded, JSON_FORCE_OBJECT);
    }

    public function uploadVideos(array $videos, array $metadata = []): string
    {
        $encoded = [];
        $userId = auth()->id();

        foreach ($videos as $key => $file) {
            $dotType = '.' . $this->safeExtension($file);

            $video = Video::create([
                'user_id' => $userId,
                'path' => 'private/media/videos/users/' . $userId . '/',
                'state' => 1,
                'album_id' => 0,
                'type' => $dotType,
                'title' => filled($metadata['title'] ?? null) ? $metadata['title'] : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'description' => $metadata['description'] ?? null,
                'seo_title' => $metadata['seo_title'] ?? null,
                'seo_description' => $metadata['seo_description'] ?? null,
                'keywords' => $metadata['keywords'] ?? null,
            ]);

            if ($video) {
                Storage::disk('local')->putFileAs($video->path, $file, $video->id . $dotType);
                $thumbnail = $metadata['thumbnail'] ?? null;
                if (is_string($thumbnail) && preg_match('/^data:image\/(?:jpeg|jpg|webp);base64,(.+)$/', $thumbnail, $matches)) {
                    $thumbnailData = base64_decode($matches[1], true);
                    if ($thumbnailData !== false && strlen($thumbnailData) <= 5 * 1024 * 1024) {
                        $thumbnailPath = $video->path.$video->id.'_thumbnail.jpg';
                        Storage::disk('local')->put($thumbnailPath, $thumbnailData);
                        $video->update(['thumbnail_path' => $thumbnailPath]);
                    }
                }
            }

            $encoded[$key] = [$video->id];

            $job = new ProcessVideoJob(
                $video->path . $video->id . $dotType,
                $video->path . $video->id,
            );
            if (config('media.sync_initial_quality', true)) {
                $job->processInitialQuality();
            }
            dispatch($job);
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
