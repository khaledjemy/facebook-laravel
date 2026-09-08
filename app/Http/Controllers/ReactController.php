<?php

namespace App\Http\Controllers;

use App\CommentReact;
use App\React;
use App\PhotoReact;
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
                    return ;
                }
           

        }else{

        
            PhotoReact::updateOrCreate(['photo_id'=>$data['photo_id'],'user_id'=>$userId],[
            'type'      =>$data['type_id'] ?? 1,
            'photo_id'	=>$request->input('photo_id'),
            'user_id'   =>$userId,
        ]);

       return ;
    }




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
    public function react_view($user_id)
    {
        
        $react  = React::where($user_id); 
      

       return ;




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
