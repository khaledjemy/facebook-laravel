<?php

namespace App\Http\Controllers;

use App\LiveStream;
use App\Services\MediaUploadService;
use App\Video;
use Illuminate\Http\Request;

class LiveStreamController extends Controller
{
    public function create()
    {
        return view('live.create', ['profile' => auth()->user()->loadMissing('photopro')]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:2000',
            'visibility' => 'required|in:public,friends,only_me',
        ]);
        $stream = LiveStream::create($data + ['user_id' => auth()->id(), 'status' => 'preparing']);
        return redirect()->route('live.show', $stream);
    }

    public function show(LiveStream $stream)
    {
        $this->canView($stream);
        $stream->load('user.photopro', 'video');
        return view('live.show', ['stream' => $stream, 'profile' => auth()->user()->loadMissing('photopro')]);
    }

    public function start(LiveStream $stream)
    {
        $this->owner($stream);
        $stream->update(['status' => 'live', 'started_at' => now(), 'ended_at' => null]);
        return response()->json(['status' => 'live']);
    }

    public function join(LiveStream $stream)
    {
        $this->canView($stream);
        abort_unless($stream->status === 'live', 409, 'البث غير متاح الآن.');
        $sessionKey = 'live_stream_joined_'.$stream->id;
        if (!session()->has($sessionKey)) {
            $stream->increment('viewer_count');
            session()->put($sessionKey, true);
            $stream->refresh();
        }
        if ($stream->viewer_count > $stream->peak_viewers) $stream->update(['peak_viewers' => $stream->viewer_count]);
        return response()->json(['viewer_count' => $stream->viewer_count]);
    }

    public function leave(LiveStream $stream)
    {
        $sessionKey = 'live_stream_joined_'.$stream->id;
        if (session()->pull($sessionKey) && $stream->viewer_count > 0) $stream->decrement('viewer_count');
        return response()->json(['viewer_count' => $stream->fresh()->viewer_count]);
    }

    public function finish(Request $request, LiveStream $stream, MediaUploadService $uploads)
    {
        $this->owner($stream);
        $request->validate(['recording' => 'nullable|file|max:512000|mimetypes:video/webm,video/mp4,video/x-matroska']);
        $stream->update(['status' => 'processing', 'viewer_count' => 0, 'ended_at' => now()]);
        if ($request->hasFile('recording')) {
            $encoded = $uploads->uploadVideos([$request->file('recording')], [
                'title' => $stream->title,
                'description' => $stream->description,
                'seo_title' => $stream->title,
            ]);
            $ids = collect(json_decode($encoded, true))->flatten();
            $stream->update(['video_id' => $ids->first()]);
        }
        $stream->update(['status' => 'ended']);
        return response()->json(['status' => 'ended', 'video_id' => $stream->video_id]);
    }

    public function status(LiveStream $stream)
    {
        $this->canView($stream);
        return response()->json($stream->only(['status', 'viewer_count', 'peak_viewers', 'video_id']));
    }

    public function comments(LiveStream $stream)
    {
        $this->canView($stream);
        return response()->json($stream->comments()->with('user.photopro')->latest()->limit(100)->get()->reverse()->values());
    }

    public function comment(Request $request, LiveStream $stream)
    {
        $this->canView($stream);
        abort_unless($stream->status === 'live', 409);
        $data = $request->validate(['body' => 'required|string|max:500']);
        return response()->json($stream->comments()->create($data + ['user_id' => auth()->id()])->load('user.photopro'));
    }

    private function owner(LiveStream $stream): void
    {
        abort_unless($stream->user_id === auth()->id(), 403);
    }

    private function canView(LiveStream $stream): void
    {
        abort_unless(LiveStream::visibleTo(auth()->user())->whereKey($stream->id)->exists(), 403);
    }
}
