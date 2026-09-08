<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Video;
use App\SavedPost;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function watch()
    {
        return view('explore', ['title' => __('ui.watch'), 'kind' => 'watch', 'items' => Video::with('user')->latest()->paginate(12)]);
    }

    public function friends()
    {
        return view('explore', ['title' => __('ui.friends'), 'kind' => 'friends', 'items' => User::whereKeyNot(auth()->id())->latest()->paginate(18)]);
    }

    public function saved()
    {
        $items = Post::with('user')->whereIn('id', SavedPost::where('user_id', auth()->id())->pluck('post_id'))->latest()->paginate(10);
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
        return view('explore', ['title' => __('ui.memories'), 'kind' => 'memories', 'items' => Post::with('user')->where('created_at', '<', now()->subDay())->latest()->paginate(10)]);
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q'));
        $users = User::with('photopro')->when($query, function ($builder) use ($query) {
            $builder->where(function ($q) use ($query) { $q->where('first_name','like',"%{$query}%")->orWhere('last_name','like',"%{$query}%")->orWhere('email','like',"%{$query}%"); });
        })->limit(20)->get();
        $posts = Post::with('user')->when($query, fn($builder) => $builder->where('post_text','like',"%{$query}%"))->latest()->limit(20)->get();
        return view('search', compact('query','users','posts'));
    }
}
