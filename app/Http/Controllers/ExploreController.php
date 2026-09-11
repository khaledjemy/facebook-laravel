<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Video;
use App\SavedPost;
use App\LiveStream;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function watch()
    {
        return view('explore', [
            'title' => __('ui.watch'),
            'kind' => 'watch',
            'items' => Video::with('user')->latest()->paginate(12),
            'liveStreams' => LiveStream::visibleTo(auth()->user())->with('user.photopro')->where('status', 'live')->latest('started_at')->get(),
        ]);
    }

    public function friends()
    {
        $me = auth()->id();

        $blockedIds = \App\Block::where('user_id', $me)->pluck('blocked_id')
            ->merge(\App\Block::where('blocked_id', $me)->pluck('user_id'))
            ->unique();

        // 1. Incoming pending friend requests (received by me)
        $incomingRequests = \App\Friend::where('friends_id', $me)
            ->where('state', 0)
            ->whereNotIn('user_id', $blockedIds)
            ->with('user.photopro')
            ->latest()
            ->get();

        // 2. Confirmed friends
        $friendRows = \App\Friend::where('state', 1)
            ->where(function ($q) use ($me) {
                $q->where('user_id', $me)->orWhere('friends_id', $me);
            })
            ->latest()
            ->get();

        $friendUserIds = $friendRows->map(function ($f) use ($me) {
            return (int) ($f->user_id == $me ? $f->friends_id : $f->user_id);
        })->reject(fn ($id) => $blockedIds->contains($id))->unique()->values();

        $friends = User::with('photopro')->whereIn('id', $friendUserIds)->get();

        // 3. Pending sent requests (sent by me)
        $sentRequestIds = \App\Friend::where('user_id', $me)
            ->where('state', 0)
            ->pluck('friends_id');

        // 4. Friend suggestions
        $excludeIds = array_values(array_unique(array_merge(
            $friendUserIds->all(),
            $incomingRequests->pluck('user_id')->all(),
            $sentRequestIds->all(),
            $blockedIds->all(),
            [$me]
        )));

        $suggestions = User::whereNotIn('id', $excludeIds)
            ->with('photopro')
            ->latest('id')
            ->limit(12)
            ->get();

        return view('friends_hub', compact('incomingRequests', 'friends', 'suggestions'));
    }

    public function saved()
    {
        $items = Post::visibleTo(auth()->user())->with('user')->whereIn('id', SavedPost::where('user_id', auth()->id())->pluck('post_id'))->latest()->paginate(10);
        return view('explore', ['title' => __('ui.saved'), 'kind' => 'saved', 'items' => $items]);
    }

    public function toggleSaved(Request $request, Post $post)
    {
        $saved = SavedPost::where(['user_id'=>auth()->id(),'post_id'=>$post->id])->first();
        if ($saved) { $saved->delete(); return response()->json(['saved'=>false]); }
        SavedPost::create(['user_id'=>auth()->id(),'post_id'=>$post->id]);
        return response()->json(['saved'=>true]);
    }

    public function memories()
    {
        return view('explore', ['title' => __('ui.memories'), 'kind' => 'memories', 'items' => Post::visibleTo(auth()->user())->with('user')->where('created_at', '<', now()->subDay())->latest()->paginate(10)]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q'));
        $users = User::with('photopro')->when($query, function ($builder) use ($query) {
            $builder->where(function ($q) use ($query) { $q->where('first_name','like',"%{$query}%")->orWhere('last_name','like',"%{$query}%")->orWhere('email','like',"%{$query}%"); });
        })->limit(20)->get();
        $posts = Post::visibleTo(auth()->user())->with('user')->when($query, fn($builder) => $builder->where('post_text','like',"%{$query}%"))->latest()->limit(20)->get();
        return view('search', compact('query','users','posts'));
    }
}
