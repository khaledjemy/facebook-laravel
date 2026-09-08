<?php

namespace App\Http\Controllers;

use App\Commente;
use App\Post;
use App\User;
use App\Photo;
use App\Video;
use Dom\Comment;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('user.photopro', 'commentes.replie.userreply.photopro','commentes.react', 'react', 'commentes.user.photopro')
            ->orderBy('created_at', 'desc')->paginate(2, ['*'], 'page', $request->query('page', $request->page));
        
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
            }
        }

       
        foreach ($posts as $post) {
            if ($post->user && !$post->user->profile_photo_id) {
                $post->user->setRelation('photopro', Photo::find(3));
            }
            foreach ($post->commentes as $comment) {
                if ($comment->user && !$comment->user->profile_photo_id) {
                    $comment->user->setRelation('photopro', Photo::find(3));
                }
            }
        }
       
      //dd($posts);
        if (auth()->check()) {
            $profilee = new UsersController;
            $profile = $profilee->profilenav();
        } else {
            $profile = null;
        }

        if ($request->ajax()) {
            
            return view('index', compact('posts', 'imges','videos', 'profile'));
        }

        return view('index', compact('posts', 'imges','videos', 'profile'));
    }

    public function store(Request $request)
    {
    //  echo'<pre>'; print_r($request); echo'</pre>';
    // return;
        $data = $request->all();
        $photo = new PhotoController;
        $photos = $request->hasFile('files') ? $request->file('files') : null;

        $validatedData = $request->validate([
            'post_text'  => 'nullable|required_without:files|max:255',
            'files'      => 'nullable|array|max:10',
            'files.*'    => 'file|max:204800|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime',
        ]);

        if($photos!=null){
            $imageResult = $photo->img($photos);
            $lol = $imageResult->original;
           // return response()->json(data: ['success' => "error", 'data' => $lol['details']['photo']]);
                if(isset($lol['error']) && $lol['error'] == "failed"){
                    $res1= $lol['error'];
                    $res2= json_decode($lol['details']) ;
                }else if(isset($lol['status']) && $lol['status'] == "ok"){

                    $userId = auth()->id();
                    $status = true;
                    $type = $lol["type"];
                    $post = Post::create([
                        'post_text'    => $validatedData['post_text'] ?? '',
                        'user_id'      => $userId, // Assign the temporary user id
                        'status'       => $status,
                        'image'        => (isset($lol['details']['photo']))?$lol['details']['photo']:null,
                        "video"        => (isset($lol['details']['video']))?$lol['details']['video']:null
                    ]);
                    $res1= $lol['status'];
                    $res2=$post;

                }
                
        }else{
            $userId = auth()->id();
            $status = true;
            $post = Post::create([
                'post_text'    => $validatedData['post_text'] ?? '',
                'user_id'      => $userId, // Assign the temporary user id
                'status'       => $status,
                'image'        => null,
                "video"        => null
            ]);
            $res1="ok";
            $res2=$post;
        }
            return response()->json(['success' => $res1, 'data' => $res2]);
      
    }

    public function index1($id)
    {
        $post = Post::where('id', $id)->with('user.photopro', 'commentes.replie.userreply.photopro', 'commentes.react','react', 'commentes.user.photopro')->firstOrFail();
       // DD($post);
        $user = User::where('id', $post->user_id)->firstOrFail();
        $imges = []; 
        $videos=[];
       // $comments = $user->commentes;
        if ($post) {
            $im_arr = json_decode($post->image, true);
            $vd_arr=json_decode($post->video, true);
            if($post->image!=null){
                if (is_array($im_arr)) { 
                    foreach ($im_arr as $key => $img) {
                        $imgess = Photo::where('user_id', $post->user_id)->where('id', $img)->get();

                        if ($imgess->isNotEmpty() ) {
                            $imges[$post->id][] = $imgess;
                        }
                    }
                }
            }else{
                $imges[$post->id]=null;
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

        if (auth()->check()) {
            $profilee = new UsersController;
            $profile = $profilee->profilenav();
            //dd($profile);
        } else {
            $profile = null;
        }

        return view('post', compact('post', 'user', 'profile', 'imges','videos'));
    }

    public function profile_post($id,Request $request)
    {
        $page = $request->query('page', 1);
        $posts = Post::where('user_id', $id)
        ->with('user.photopro','commentes.react', 'commentes.replie.userreply.photopro', 'react', 'commentes.user.photopro')
        ->orderBy('created_at', 'desc')
        ->paginate(2, ['*'], 'page', $page);
        return $posts;
    }
public function delete_post(Request  $request)
    {
        $post = Post::find($request->id);
        if ($post) {
            $post->delete();
            return response()->json(['success' => 'Post deleted successfully']);
        } else {
            return response()->json(['error' => 'Post not found'], 404);
        }
    }
}
