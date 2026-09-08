<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\ApiToken;

class V1Controller extends Controller
{
    public function login(Request $request)
    {
        $data=$request->validate(['email'=>'required|email','password'=>'required|string','device_name'=>'nullable|string|max:80']);
        $user=User::where('email',$data['email'])->first();
        if(!$user || !Hash::check($data['password'],$user->password)) return response()->json(['message'=>'Invalid credentials.'],422);
        $plain=Str::random(64);
        ApiToken::create(['user_id'=>$user->id,'name'=>$data['device_name'] ?? 'api','token_hash'=>hash('sha256',$plain),'expires_at'=>now()->addDays(30)]);
        return response()->json(['token'=>$plain,'token_type'=>'Bearer','expires_in'=>2592000]);
    }

    public function me(Request $request) { return response()->json($request->user()->load('photopro')); }
    public function logout(Request $request) { $request->attributes->get('apiToken')->delete(); return response()->json(['message'=>'Token revoked.']); }
    public function createPost(Request $request) { $data=$request->validate(['post_text'=>'required|string|max:255']); return response()->json(Post::create(['user_id'=>$request->user()->id,'post_text'=>$data['post_text'],'status'=>1]),201); }
    public function feed(Request $request)
    {
        $limit = min(max((int) $request->query('limit', 10), 1), 50);
        return response()->json(Post::with(['user.photopro','react.user'])->latest()->paginate($limit));
    }

    public function post(Post $post)
    {
        return response()->json($post->load(['user.photopro','commentes.user.photopro','react.user']));
    }

    public function users(Request $request)
    {
        $query = trim((string) $request->query('q'));
        return response()->json(User::with('photopro')->when($query, function ($builder) use ($query) {
            $builder->where(function ($q) use ($query) { $q->where('first_name','like',"%{$query}%")->orWhere('last_name','like',"%{$query}%"); });
        })->select('id','first_name','last_name','profile_photo_id')->limit(25)->get());
    }

    public function user(User $user)
    {
        return response()->json($user->load(['photopro','coverpro'])->makeHidden(['email','password','remember_token']));
    }
}
