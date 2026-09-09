<?php

namespace App\Http\Controllers;

use App\Hashtag;
use App\Photo;
use App\Video;

class HashtagController extends Controller
{
    public function show(string $slug)
    {
        $hashtag = Hashtag::where('slug', $slug)->firstOrFail();
        $posts = $hashtag->posts()->visibleTo(auth()->user())
            ->with(['user.photopro', 'react', 'commentes'])
            ->latest('posts.created_at')->paginate(10);
        $imges = $videos = [];
        foreach ($posts as $post) {
            $imges[$post->id] = collect(json_decode($post->image, true) ?: [])->map(fn ($id) => Photo::whereKey($id)->get())->all();
            $videos[$post->id] = collect(json_decode($post->video, true) ?: [])->map(fn ($id) => Video::whereKey($id)->get())->all();
        }
        $profile = auth()->user()->loadMissing('photopro', 'coverpro');

        return view('hashtag', compact('hashtag', 'posts', 'imges', 'videos', 'profile'));
    }
}
