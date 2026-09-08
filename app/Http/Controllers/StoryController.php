<?php

namespace App\Http\Controllers;

use App\Block;
use App\Friend;
use App\Services\MediaUploadService;
use App\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $me = auth()->id();

        $blockedIds = Block::where('user_id', $me)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $me)->pluck('user_id'))
            ->unique()
            ->values();

        $friendIds = Friend::where('state', 1)
            ->where(function ($q) use ($me) {
                $q->where('user_id', $me)->orWhere('friends_id', $me);
            })
            ->get(['user_id', 'friends_id'])
            ->map(fn ($f) => (int) ($f->user_id == $me ? $f->friends_id : $f->user_id))
            ->reject(fn ($id) => $blockedIds->contains($id))
            ->unique()
            ->values();

        $allowedUserIds = collect([$me])->merge($friendIds)->unique()->values();

        $stories = Story::with('user.photopro')
            ->whereIn('user_id', $allowedUserIds)
            ->active()
            ->orderBy('created_at', 'asc')
            ->get();

        $grouped = $stories->groupBy('user_id')->map(function ($userStories) {
            $user = $userStories->first()->user;
            return [
                'user_id' => $user->id,
                'user_name' => $user->first_name . ' ' . $user->last_name,
                'user_avatar' => $user->avatar_url,
                'stories' => $userStories->map(fn ($s) => [
                    'id' => $s->id,
                    'type' => $s->type,
                    'media_url' => $s->media_path ? asset($s->media_path) : null,
                    'content' => $s->content,
                    'background' => $s->background,
                    'time_ago' => $s->created_at->diffForHumans(short: true),
                    'created_at' => $s->created_at->toIso8601String(),
                    'expires_at' => $s->expires_at->toIso8601String(),
                ]),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'data' => $grouped,
        ]);
    }

    public function store(Request $request, ?MediaUploadService $uploadService = null)
    {
        $data = $request->validate([
            'type' => 'required|string|in:text,image,video',
            'content' => 'nullable|string|max:1000',
            'background' => 'nullable|string|max:255',
            'media' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp4,mov,webm|max:51200',
        ]);

        if ($data['type'] === 'text' && empty(trim($data['content'] ?? ''))) {
            return response()->json([
                'status' => false,
                'message' => 'Text story requires content.',
            ], 422);
        }

        if (in_array($data['type'], ['image', 'video'], true) && !$request->hasFile('media')) {
            return response()->json([
                'status' => false,
                'message' => 'Media story requires a media file.',
            ], 422);
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $uploadService ??= app(MediaUploadService::class);
            $file = $request->file('media');
            $ext = $uploadService->safeExtension($file);
            $filename = uniqid('story_', true) . '.' . $ext;
            $destinationDir = public_path('stories/' . auth()->id());

            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $file->move($destinationDir, $filename);
            $mediaPath = 'stories/' . auth()->id() . '/' . $filename;
        }

        $story = Story::create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'media_path' => $mediaPath,
            'content' => $data['content'] ?? null,
            'background' => $data['background'] ?? null,
            'expires_at' => now()->addHours(24),
        ]);

        return response()->json([
            'status' => true,
            'story' => $story->load('user.photopro'),
        ], 201);
    }

    public function destroy($id)
    {
        $story = Story::findOrFail($id);

        if ((int) $story->user_id !== (int) auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized to delete this story.',
            ], 403);
        }

        if ($story->media_path && is_file(public_path($story->media_path))) {
            @unlink(public_path($story->media_path));
        }

        $story->delete();

        return response()->json([
            'status' => true,
            'message' => 'Story deleted successfully.',
        ]);
    }
}
