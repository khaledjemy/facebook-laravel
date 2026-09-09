<?php

namespace App\Http\Controllers;

use App\Commente;
use App\Post;
use App\User;
use App\Photo;
use App\Video;
use App\Hashtag;
use App\Support\HashtagFormatter;
use App\Services\MediaUploadService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::visibleTo(auth()->user())
            ->with('user.photopro', 'sharedPost.user.photopro', 'commentes.replie.userreply.photopro','commentes.react', 'react', 'commentes.user.photopro')
            ->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', $request->query('page', $request->page));
        
        $imges = []; 
        $videos= [];
        
        if ($posts->isNotEmpty()) {
            foreach ($posts as $post) {
                $imges[$post->id]  = [];
                $videos[$post->id] = [];
                
                $im_arr = json_decode($post->image, true);
                $vd_arr = json_decode($post->video, true);

                if (is_array($im_arr)) { 
                    foreach ($im_arr as $key => $img) {
                        $imgess = Photo::where('user_id', $post->user_id)->where('id', $img)->get();
                        if ($imgess->isNotEmpty()) {
                            $imges[$post->id][] = $imgess;
                        }
                    }
                }
                if (is_array($vd_arr)) { 
                    foreach ($vd_arr as $key => $vid) {
                        $videoss = Video::where('user_id', $post->user_id)->where('id', $vid)->get();
                        if ($videoss->isNotEmpty()) {
                            $videos[$post->id][] = $videoss;
                        }
                    }
                }

                // If this is a shared post, also load original post media
                if ($post->shared_post_id && $post->sharedPost) {
                    $sPost = $post->sharedPost;
                    if (!isset($imges[$sPost->id])) {
                        $imges[$sPost->id] = [];
                        $s_im_arr = json_decode($sPost->image, true);
                        if (is_array($s_im_arr)) {
                            foreach ($s_im_arr as $img) {
                                $sImg = Photo::where('user_id', $sPost->user_id)->where('id', $img)->get();
                                if ($sImg->isNotEmpty()) {
                                    $imges[$sPost->id][] = $sImg;
                                }
                            }
                        }
                    }
                    if (!isset($videos[$sPost->id])) {
                        $videos[$sPost->id] = [];
                        $s_vd_arr = json_decode($sPost->video, true);
                        if (is_array($s_vd_arr)) {
                            foreach ($s_vd_arr as $vid) {
                                $sVid = Video::where('user_id', $sPost->user_id)->where('id', $vid)->get();
                                if ($sVid->isNotEmpty()) {
                                    $videos[$sPost->id][] = $sVid;
                                }
                            }
                        }
                    }
                }
            }
        }

        $profile = auth()->check() ? auth()->user()->loadMissing('photopro', 'coverpro') : null;

        if ($request->ajax()) {
            return view('index', compact('posts', 'imges','videos', 'profile'));
        }

        return view('index', compact('posts', 'imges','videos', 'profile'));
    }

    public function store(Request $request, ?MediaUploadService $uploadService = null)
    {
        $uploadService = $uploadService ?: app(MediaUploadService::class);
        $photos = $request->hasFile('files') ? $request->file('files') : null;

        $validatedData = $request->validate([
            'post_text'  => 'nullable|required_without:files|max:255',
            'files'      => 'nullable|array|max:10',
            'files.*'    => 'file|max:204800|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,video/x-matroska,video/avi,video/x-msvideo,video/mpeg,video/3gpp',
            'visibility' => 'sometimes|in:public,friends,only_me',
            'video_title' => 'nullable|string|max:160',
            'video_description' => 'nullable|string|max:2000',
            'video_seo_title' => 'nullable|string|max:160',
            'video_seo_description' => 'nullable|string|max:500',
            'video_keywords' => 'nullable|string|max:500',
            'hashtags' => 'nullable|string|max:500',
            'selected_video_thumbnail' => 'nullable|string|max:7000000',
        ]);

        $userId = auth()->id();
        $status = true;

        if ($photos != null) {
            try {
                $uploadResult = $uploadService->processUploads($photos, [
                    'title' => $validatedData['video_title'] ?? null,
                    'description' => $validatedData['video_description'] ?? null,
                    'seo_title' => $validatedData['video_seo_title'] ?? null,
                    'seo_description' => $validatedData['video_seo_description'] ?? null,
                    'keywords' => $validatedData['video_keywords'] ?? null,
                    'thumbnail' => $validatedData['selected_video_thumbnail'] ?? null,
                ]);
            } catch (\Throwable $exception) {
                report($exception);
                return response()->json([
                    'message' => 'تم رفع الملف لكن تعذر تجهيز أول جودة للفيديو، لذلك لم يتم نشره. حاول مرة أخرى.',
                ], 422);
            }

            if (isset($uploadResult['error']) && $uploadResult['error'] === 'failed') {
                return response()->json(['success' => 'failed', 'data' => $uploadResult['details']]);
            }

            $post = Post::create([
                'post_text'  => $validatedData['post_text'] ?? '',
                'user_id'    => $userId,
                'status'     => $status,
                'image'      => $uploadResult['details']['photo'] ?? null,
                'video'      => $uploadResult['details']['video'] ?? null,
                'visibility' => $validatedData['visibility'] ?? 'public',
            ]);

            $this->syncHashtags($post, ($validatedData['hashtags'] ?? '').' '.($validatedData['video_description'] ?? ''));

            return response()->json(['success' => 'ok', 'data' => $post]);
        }

        $post = Post::create([
            'post_text'  => $validatedData['post_text'] ?? '',
            'user_id'    => $userId,
            'status'     => $status,
            'image'      => null,
            'video'      => null,
            'visibility' => $validatedData['visibility'] ?? 'public',
        ]);

        $this->syncHashtags($post, $validatedData['hashtags'] ?? '');

        return response()->json(['success' => 'ok', 'data' => $post]);
    }

    private function syncHashtags(Post $post, string $extraText = ''): void
    {
        $previousIds = $post->hashtags()->pluck('hashtags.id');
        $ids = collect(HashtagFormatter::extract($post->post_text.' '.$extraText))->map(function (string $name) {
            return Hashtag::firstOrCreate(
                ['slug' => HashtagFormatter::slug($name)],
                ['name' => $name]
            )->id;
        });

        $post->hashtags()->sync($ids);
        Hashtag::whereIn('id', $previousIds->merge($ids)->unique())->withCount('posts')->get()->each(
            fn (Hashtag $tag) => $tag->update(['posts_count' => $tag->posts_count])
        );
    }

    public function index1($id)
    {
        $post = Post::visibleTo(auth()->user())->where('id', $id)->with('user.photopro', 'commentes.replie.userreply.photopro', 'commentes.react','react', 'commentes.user.photopro')->firstOrFail();
        $user = User::where('id', $post->user_id)->firstOrFail();
        $imges = []; 
        $videos=[];

        if ($post) {
            $im_arr = json_decode($post->image, true);
            $vd_arr = json_decode($post->video, true);
            if ($post->image != null) {
                if (is_array($im_arr)) { 
                    foreach ($im_arr as $key => $img) {
                        $imgess = Photo::where('user_id', $post->user_id)->where('id', $img)->get();

                        if ($imgess->isNotEmpty()) {
                            $imges[$post->id][] = $imgess;
                        }
                    }
                }
            } else {
                $imges[$post->id] = null;
                if (is_array($vd_arr)) { 
                    foreach ($vd_arr as $key => $vid) {
                        $videoss = Video::where('user_id', $post->user_id)->where('id', $vid)->get();
                        if ($videoss->isNotEmpty()) {
                            $videos[$post->id][] = $videoss;
                        }
                    }
                }
            }
        }

        $profile = auth()->check() ? auth()->user()->loadMissing('photopro', 'coverpro') : null;

        return view('post', compact('post', 'user', 'profile', 'imges','videos'));
    }

    public function profile_post($id,Request $request)
    {
        $page = $request->query('page', 1);
        $posts = Post::visibleTo(auth()->user())->where('user_id', $id)
        ->with('user.photopro','commentes.react', 'commentes.replie.userreply.photopro', 'react', 'commentes.user.photopro')
        ->orderBy('created_at', 'desc')
        ->paginate(2, ['*'], 'page', $page);
        return $posts;
    }

    public function delete_post(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:posts,id',
        ]);

        $post = Post::findOrFail($validatedData['id']);

        if ((int) $post->user_id !== (int) auth()->id()) {
            return response()->json(['error' => 'Unauthorized action. You cannot delete this post.'], 403);
        }

        $post->delete();

        return response()->json(['success' => 'Post deleted successfully']);
    }

    public function share(Request $request, $id)
    {
        $me = auth()->user();
        $originalPost = Post::findOrFail($id);

        $canSee = Post::whereKey($id)->visibleTo($me)->exists();
        abort_unless($canSee, 403, 'You are not allowed to view or share this post.');

        $data = $request->validate([
            'post_text' => 'nullable|string|max:1000',
            'visibility' => 'nullable|string|in:public,friends,only_me',
        ]);

        $sharedPost = Post::create([
            'user_id' => $me->id,
            'post_text' => $data['post_text'] ?? null,
            'shared_post_id' => $originalPost->id,
            'visibility' => $data['visibility'] ?? 'public',
            'status' => 1,
        ]);
        $this->syncHashtags($sharedPost);

        if ((int) $originalPost->user_id !== (int) $me->id) {
            $originalOwner = User::find($originalPost->user_id);
            if ($originalOwner) {
                $originalOwner->notify(new \App\Notifications\PostSharedNotification($me, $originalPost, $sharedPost));
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Post shared successfully!',
                'post' => $sharedPost->load('user.photopro', 'sharedPost.user.photopro'),
            ]);
        }

        return redirect()->back()->with('success', 'Post shared successfully!');
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        if ((int) $post->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized. You cannot edit this post.');
        }

        $validated = $request->validate([
            'post_text' => 'nullable|string|max:1000',
            'visibility' => 'sometimes|in:public,friends,only_me',
        ]);

        $post->update([
            'post_text' => $validated['post_text'] ?? $post->post_text,
            'visibility' => $validated['visibility'] ?? $post->visibility,
        ]);
        $this->syncHashtags($post);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'تم تحديث المنشور بنجاح.',
                'post' => $post,
            ]);
        }

        return back()->with('success', 'تم تحديث المنشور بنجاح.');
    }
}
