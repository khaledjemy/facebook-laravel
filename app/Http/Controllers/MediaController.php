<?php

namespace App\Http\Controllers;

use App\Commente;
use App\LiveStream;
use App\Post;
use App\Services\MediaVisibilityService;
use App\Story;
use App\Video;
use App\photo as Photo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function photo(Photo $photo, MediaVisibilityService $visibility): BinaryFileResponse
    {
        abort_unless($visibility->canViewPhoto($photo, auth()->user()), 404);
        $relativePath = trim((string) $photo->path, '/').'/'.$photo->id.$photo->type;

        return $this->file($relativePath, '/^(?:private\/media\/images\/users\/'.(int) $photo->user_id.'|images\/users\/'.(int) $photo->user_id.')\/'.$photo->id.'\.(?:jpg|jpeg|png|gif|webp)$/i');
    }

    public function videoHls(Video $video, string $file, MediaVisibilityService $visibility): BinaryFileResponse
    {
        abort_unless($visibility->canViewVideo($video, auth()->user()), 404);
        abort_unless(preg_match('/^(?:playlist|(?:144|240|360|480|720|1080)p(?:_\d{3,})?)\.(?:m3u8|ts)$/', $file), 404);

        $basePath = trim((string) $video->path, '/').'/'.$video->id;
        $expectedPrefix = preg_quote($basePath, '/');
        abort_unless(preg_match('/^(?:private\/media\/videos\/users|video\/users)\/'.(int) $video->user_id.'\/'.$video->id.'$/', $basePath), 404);

        return $this->file($basePath.'/'.$file, '/^'.$expectedPrefix.'\/(?:playlist|(?:144|240|360|480|720|1080)p(?:_\d{3,})?)\.(?:m3u8|ts)$/');
    }

    public function videoThumbnail(Video $video, MediaVisibilityService $visibility): BinaryFileResponse
    {
        abort_unless($visibility->canViewVideo($video, auth()->user()), 404);
        abort_unless($video->thumbnail_path, 404);
        $relativePath = trim((string) $video->thumbnail_path, '/');
        $allowed = '/^(?:private\/media\/videos\/users\/'.(int) $video->user_id.'|video\/users\/'.(int) $video->user_id.')\/'.$video->id.'_thumbnail\.jpg$/';

        return $this->file($relativePath, $allowed);
    }

    public function story(Story $story): BinaryFileResponse
    {
        $viewerId = (int) auth()->id();
        abort_unless($story->expires_at?->isFuture(), 404);

        $blocked = \App\Block::where(function ($query) use ($viewerId, $story) {
            $query->where('user_id', $viewerId)->where('blocked_id', $story->user_id);
        })->orWhere(function ($query) use ($viewerId, $story) {
            $query->where('user_id', $story->user_id)->where('blocked_id', $viewerId);
        })->exists();
        abort_if($blocked, 404);

        $isVisible = (int) $story->user_id === $viewerId || \App\Friend::where('state', 1)
            ->where(function ($query) use ($viewerId, $story) {
                $query->where('user_id', $viewerId)->where('friends_id', $story->user_id)
                    ->orWhere('user_id', $story->user_id)->where('friends_id', $viewerId);
            })->exists();
        abort_unless($isVisible, 404);

        $relativePath = trim((string) $story->media_path, '/');
        $allowed = '/^(?:private\/stories|stories)\/'.(int) $story->user_id.'\/story_[a-zA-Z0-9._-]+\.(?:jpg|jpeg|png|gif|webp|mp4|mov|webm)$/i';

        return $this->file($relativePath, $allowed);
    }

    public function comment(Commente $comment, string $file, MediaVisibilityService $visibility): BinaryFileResponse
    {
        abort_unless(Post::visibleTo(auth()->user())->whereKey($comment->post_id)->exists(), 404);
        return $this->commentMediaFile($comment->media_path, $file, (int) $comment->user_id);
    }

    public function reply(\App\Replie $reply, string $file, MediaVisibilityService $visibility): BinaryFileResponse
    {
        $comment = Commente::findOrFail($reply->comment_id);
        abort_unless(Post::visibleTo(auth()->user())->whereKey($comment->post_id)->exists(), 404);
        return $this->commentMediaFile($reply->media_path, $file, (int) $reply->user_id);
    }

    private function commentMediaFile(?string $mediaPath, string $file, int $userId): BinaryFileResponse
    {
        abort_unless($mediaPath && preg_match('/^[a-f0-9]{32}\.[a-z0-9]{1,8}$|^(?:playlist|(?:144|240|360|480|720|1080)p(?:_\d{3,})?)\.(?:m3u8|ts)$/i', $file), 404);
        $base = trim(str_replace('\\', '/', dirname($mediaPath)), '/');
        abort_unless(preg_match('/^(?:private\/)?comment-media\/'.$userId.'(?:\/(?:commentes|replies)-\d+)?$/', $base), 404);
        return $this->file($base.'/'.$file, '/^'.preg_quote($base, '/').'\/'.$file.'$/');
    }

    private function file(string $relativePath, string $allowedPattern): BinaryFileResponse
    {
        abort_unless(preg_match($allowedPattern, $relativePath), 404);

        if (str_starts_with($relativePath, 'private/')) {
            $candidate = Storage::disk('local')->path($relativePath);
            $root = realpath(Storage::disk('local')->path(''));
        } else {
            $root = realpath(public_path());
            $candidate = public_path($relativePath);
        }

        $resolved = realpath($candidate);
        abort_unless($root && $resolved && is_file($resolved) && str_starts_with($resolved, $root.DIRECTORY_SEPARATOR), 404);

        $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts' => 'video/mp2t',
            default => mime_content_type($resolved) ?: 'application/octet-stream',
        };

        return new BinaryFileResponse($resolved, 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ], false);
    }
}
