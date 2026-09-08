<?php

namespace App\Http\Controllers;

use App\CommentReact;
use App\Notifications\NewReactionNotification;
use App\Post;
use App\React;
use App\PhotoReact;
use App\VideoReact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReactController extends Controller
{
    //
    public function react(Request $request)
    {
        $data = $request->validate(['post_id'=>'required|integer|exists:posts,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
 
        if($request->input('liked')==true)
        {
            
            $react = React::where('post_id', $request->input('post_id'))
                    ->where('user_id', $userId)
                    ->first();

            if($react) {
                $react->delete();
                return ;
            
            }
           

        }else{

        
            React::updateOrCreate(['post_id'=>$data['post_id'],'user_id'=>$userId],[
                'type'      =>$data['type_id'] ?? 1,
                'post_id'	=>$request->input('post_id'),
                'user_id'   =>$userId,
            ]);

            $post = Post::with('user')->find($data['post_id']);
            if ($post && $post->user && (int) $post->user_id !== (int) $userId) {
                $post->user->notify(new NewReactionNotification(auth()->user(), $post, (int) ($data['type_id'] ?? 1)));
            }

            return ;
        }




    }
    public function react_photo(Request $request)
    {
        $data = $request->validate(['photo_id'=>'required|integer|exists:photos,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
 
        if($request->input('liked')==true)
        {
          //  dd($request->input('user_id'));
                $react = PhotoReact::where('photo_id', $request->input('photo_id'))
                    ->where('user_id', $userId)
                    ->first();
                if($react) {
                    $react->delete();
                    return response()->json(['status'=>'ok','liked'=>false,'count'=>PhotoReact::where('photo_id',$data['photo_id'])->count(),'media_type'=>'photo','media_id'=>(int)$data['photo_id']]);
                }
           

        }else{

        
            PhotoReact::updateOrCreate(['photo_id'=>$data['photo_id'],'user_id'=>$userId],[
            'type'      =>$data['type_id'] ?? 1,
            'photo_id'	=>$request->input('photo_id'),
            'user_id'   =>$userId,
        ]);

       return response()->json(['status'=>'ok','liked'=>true,'count'=>PhotoReact::where('photo_id',$data['photo_id'])->count(),'media_type'=>'photo','media_id'=>(int)$data['photo_id']]);
        }
    }

    public function react_video(Request $request)
    {
        $data = $request->validate([
            'video_id' => 'required|integer|exists:videos,id',
            'type_id' => 'nullable|integer|between:1,7',
            'liked' => 'nullable',
        ]);
        $userId = auth()->id();

        $reaction = VideoReact::where('video_id', $data['video_id'])
            ->where('user_id', $userId)
            ->first();

        if ($request->boolean('liked')) {
            $reaction?->delete();
            $liked = false;
        } else {
            VideoReact::updateOrCreate(
                ['video_id' => $data['video_id'], 'user_id' => $userId],
                ['type' => $data['type_id'] ?? 1]
            );
            $liked = true;
        }

        return response()->json([
            'status' => 'ok',
            'liked' => $liked,
            'count' => VideoReact::where('video_id', $data['video_id'])->count(),
            'media_type' => 'video',
            'media_id' => (int) $data['video_id'],
        ]);
    }
    public function react_comment(Request $request)
    {
        $data = $request->validate(['comment_id'=>'required|integer|exists:commentes,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
 
        if($request->input('liked')==true)
        {
                $react = CommentReact::where('comment_id', $request->input('comment_id'))
                    ->where('user_id', $userId)
                    ->first();
                if($react) {
                    $react->delete();
                    return ;
                }
           

        }else{

        
        CommentReact::updateOrCreate(['comment_id'=>$data['comment_id'],'user_id'=>$userId],[
            'type'      =>$data['type_id'] ?? 1,
            'comment_id'	=>$request->input('comment_id'),
            'user_id'   =>$userId,
        ]);

       return ;
    }
}

    public function postReactions($post)
    {
        $labels = [1 => 'Like', 2 => 'Love', 3 => 'Care', 4 => 'Haha', 5 => 'Wow', 6 => 'Sad', 7 => 'Angry'];
        $reactions = React::with('user.photopro')->where('post_id', $post)->latest()->get()->map(function ($reaction) use ($labels) {
            $photo = $reaction->user?->photopro;
            return [
                'name' => trim(($reaction->user->first_name ?? '') . ' ' . ($reaction->user->last_name ?? '')),
                'profile_url' => url('/profile/'.($reaction->user_id ?? 0)),
                'avatar' => $photo ? asset($photo->path.$photo->id.$photo->type) : asset('img/Default_avatar_profile.jpg'),
                'type' => $labels[$reaction->type] ?? 'Like',
                'type_id' => (int) $reaction->type,
                'emoji' => [1=>'👍',2=>'❤️',3=>'🤗',4=>'😂',5=>'😮',6=>'😢',7=>'😡'][$reaction->type] ?? '👍',
            ];
        });
        return response()->json($reactions);
    }
}
