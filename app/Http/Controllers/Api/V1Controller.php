<?php

namespace App\Http\Controllers\Api;

use App\ApiToken;
use App\Commente;
use App\Http\Controllers\Controller;
use App\Post;
use App\React;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class V1Controller extends Controller
{
    public function login(Request $request)
    {
        $data=$request->validate(['email'=>'required|email','password'=>'required|string','device_name'=>'nullable|string|max:80']);
        $user=User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password)) return response()->json(['message'=>'Invalid credentials.'],422);
        if ($user->is_active === false) return response()->json(['message'=>'This account is suspended.'],403);
        if (!$user->hasVerifiedEmail()) return response()->json(['message'=>'Verify your email before using the API.'],403);
        $plain=Str::random(64);
        ApiToken::create(['user_id'=>$user->id,'name'=>$data['device_name'] ?? 'api','token_hash'=>hash('sha256',$plain),'expires_at'=>now()->addDays(30)]);
        return response()->json(['token'=>$plain,'token_type'=>'Bearer','expires_in'=>2592000]);
    }

    public function me(Request $request) { return response()->json($request->user()->load('photopro')); }
    public function logout(Request $request) { $request->attributes->get('apiToken')->delete(); return response()->json(['message'=>'Token revoked.']); }
    public function createPost(Request $request) { $data=$request->validate(['post_text'=>'required|string|max:255','visibility'=>'sometimes|in:public,friends,only_me']); return response()->json(Post::create(['user_id'=>$request->user()->id,'post_text'=>$data['post_text'],'status'=>1,'visibility'=>$data['visibility'] ?? 'public']),201); }
    public function feed(Request $request)
    {
        $limit = min(max((int) $request->query('limit', 10), 1), 50);
        $posts = Post::visibleTo($request->user())
            ->with(['user.photopro', 'user.coverpro', 'react.user.photopro', 'react.user.coverpro'])
            ->latest()->paginate($limit);
        $posts->getCollection()->transform(fn (Post $post) => $this->publicPost($post));

        return response()->json($posts);
    }

    public function post(Post $post)
    {
        abort_unless(Post::visibleTo(request()->user())->whereKey($post->id)->exists(), 404);
        $post->load([
            'user.photopro', 'user.coverpro',
            'react.user.photopro', 'react.user.coverpro',
            'commentes' => fn ($query) => $query->latest()->limit(100),
            'commentes.user.photopro', 'commentes.user.coverpro',
        ]);

        return response()->json($this->publicPost($post, true));
    }

    public function users(Request $request)
    {
        $query = trim((string) $request->query('q'));
        return response()->json(User::where('is_active', true)->with(['photopro', 'coverpro'])->when($query, function ($builder) use ($query) {
            $builder->where(function ($q) use ($query) { $q->where('first_name','like',"%{$query}%")->orWhere('last_name','like',"%{$query}%"); });
        })->limit(25)->get()->map(fn (User $user) => $this->publicUser($user)));
    }

    public function user(User $user)
    {
        abort_if($user->is_active === false, 404);

        return response()->json($this->publicUser($user->load(['photopro', 'coverpro'])));
    }

    private function publicPost(Post $post, bool $withComments = false): array
    {
        $data = [
            'id' => $post->id,
            'user' => $post->user ? $this->publicUser($post->user) : null,
            'post_text' => $post->post_text,
            'image' => $post->image,
            'video' => $post->video,
            'visibility' => $post->visibility,
            'shared_post_id' => $post->shared_post_id,
            'created_at' => $post->created_at,
            'reactions' => $post->react->map(fn (React $reaction) => [
                'type' => $reaction->type,
                'user' => $reaction->user ? $this->publicUser($reaction->user) : null,
            ])->values(),
        ];

        if ($withComments) {
            $data['comments'] = $post->commentes->map(fn (Commente $comment) => [
                'id' => $comment->id,
                'text' => $comment->text_co,
                'media_url' => $comment->media_url,
                'media_type' => $comment->media_type,
                'created_at' => $comment->created_at,
                'user' => $comment->user ? $this->publicUser($comment->user) : null,
            ])->values();
        }

        return $data;
    }

    private function publicUser(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'about' => $user->about,
            'avatar_url' => $user->avatar_url,
            'cover_url' => $user->cover_url,
            'created_at' => $user->created_at,
        ];
    }
}
