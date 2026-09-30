<?php

namespace App\Services;

use App\LiveStream;
use App\Block;
use App\Post;
use App\photo as Photo;
use App\User;
use App\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class MediaVisibilityService
{
    /** @return Collection<int, int> */
    public function visibleVideoIds(?User $viewer): Collection
    {
        $postVideoIds = Post::visibleTo($viewer)
            ->whereNotNull('video')
            ->pluck('video')
            ->flatMap(function ($encoded) {
                $decoded = json_decode((string) $encoded, true);
                return is_array($decoded) ? Arr::flatten($decoded) : [];
            })
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id);

        $liveVideos = $viewer
            ? LiveStream::visibleTo($viewer)->whereNotNull('video_id')->pluck('video_id')
            : LiveStream::where('visibility', 'public')->whereNotNull('video_id')->pluck('video_id');

        return $postVideoIds->merge($liveVideos)->unique()->values();
    }

    public function canViewVideo(Video $video, ?User $viewer): bool
    {
        if ($viewer && (int) $video->user_id === (int) $viewer->id) {
            return true;
        }

        return $this->visibleVideoIds($viewer)->contains((int) $video->id);
    }

    /** @return Collection<int, int> */
    public function visiblePhotoIds(?User $viewer): Collection
    {
        return Post::visibleTo($viewer)
            ->whereNotNull('image')
            ->pluck('image')
            ->flatMap(function ($encoded) {
                $decoded = json_decode((string) $encoded, true);
                return is_array($decoded) ? Arr::flatten($decoded) : [];
            })
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function canViewPhoto(Photo $photo, ?User $viewer): bool
    {
        if ($viewer && (int) $photo->user_id === (int) $viewer->id) {
            return true;
        }

        if ($viewer && Block::where(function ($query) use ($viewer, $photo) {
            $query->where('user_id', $viewer->id)->where('blocked_id', $photo->user_id);
        })->orWhere(function ($query) use ($viewer, $photo) {
            $query->where('user_id', $photo->user_id)->where('blocked_id', $viewer->id);
        })->exists()) {
            return false;
        }

        $owner = User::find($photo->user_id);
        if ($owner && ($owner->profile_photo_id == $photo->id || $owner->cover_photo_id == $photo->id)) {
            return true;
        }

        return $this->visiblePhotoIds($viewer)->contains((int) $photo->id);
    }
}
