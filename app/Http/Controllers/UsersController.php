<?php

namespace App\Http\Controllers;

use App\Album;
use App\Friend;
use App\Http\Controllers\PostController;
use App\User;
use App\photo;
use App\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Collection\Map\AssociativeArrayMap;

class UsersController extends Controller
{
    //
    public function profile($id ,Request $request)
    {
    $allphoto       =   photo::where('user_id',$id)->get();
    $albums         =   Album::where('user_id',$id)->with('photos')->get();
    $friends        =   $this->friends($id);
    $profile_page   =   User::where('id',$id)->with('photopro','coverpro')->firstOrFail();
    $posts          =   new PostController;
    $p_postes= $posts->profile_post($id,$request);

    $imges = [];
    $videos= [];

    if ($p_postes->isNotEmpty()) {
       // $imges  =Null;
       
        foreach ($p_postes as $post) {
           
            $imges[$post->id] = [];
            $videos[$post->id]= [];            
            $im_arr = json_decode($post->image, true);
            $vd_arr = json_decode($post->video, true);
           if($post->image!=null){
            if (is_array($im_arr)) { 
                foreach ($im_arr as $key => $img) {
                    $imgess = Photo::where('user_id', $post->user_id)->where('id', $img)->get();
                    if ($imgess->isNotEmpty()) {
                        $imges[$post->id][] = $imgess;
                    }
                }
                }
            }
            if($post->video!=null){
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
            
        }
        if (auth()->check()) {
            $profilee = new UsersController;
            $profile = $profilee->profilenav();
        } else {
            $profile = null;
        }
        if(!$profile_page->photopro){
            $profile_page->setRelation('photopro', Photo::find(3));

        }

    
        return view("profile",compact('profile_page','p_postes','friends','imges','videos','allphoto','albums','profile'));
    }
    public function profilenav()
    {
        $id=auth()->id();
    $profile   =  User::where('id',$id)->with('photopro','coverpro')->firstOrFail();
    //$profile->setRelation('photopro', Photo::find(3));

        
        return $profile;
    }
    public function friends($id)
    {
        $me     =   auth()->id();
        $other  =   User::where('id',$id)->firstOrFail();

        $friends =   Friend::where('user_id',$me)->orWhere('friends_id', $me)->get();
        if($me==$id){
            return $friends="yourProfile";
        }else{
            $result = [4, null, null,$friends]; 

            foreach ($friends as $friend) {
                if (($friend->user_id == $id && $friend->friends_id == $me) ||
                    ($friend->user_id == $me && $friend->friends_id == $id)) {
                    if ($friend->state == 1) {
                        $result = [1, $friend->user_id, $friend->friends_id,$friends]; // friend
                    } elseif ($friend->state == 0) {
                        if ($friend->user_id == $me && $friend->friends_id == $id) {
                            $result = [2, $friend->user_id, $friend->friends_id,$friends]; // cancel
                        } elseif ($friend->friends_id == $me && $friend->user_id == $id) {
                            $result = [3, $friend->user_id, $friend->friends_id,$friends]; // accept
                        }
                    }
                    break; 
                }
            }
        }

        return $result;
    }
    public function friend_action(Request $request)
    {
        $add            =   new Friend;
        if($request['type']=="add"){
        $add->user_id   =   auth()->id();
        $add->friends_id=   $request['user'];
        $add->state     =   0;
        $add->save();
        }elseif($request['type']=="cancel"){
            Friend::where('user_id',auth()->id())->where('friends_id',$request['user'])->delete();
        }elseif($request['type']=="accept"){
            Friend::where('friends_id',auth()->id())->where('user_id',$request['user'])->update(['state'=>true]);
        }elseif($request['type']=="cancel_r"){
            Friend::where('friends_id',auth()->id())->where('user_id',$request['user'])->delete();

        }elseif($request['type']=="remove"){
            Friend::where('friends_id',auth()->id())->Where('user_id',$request['user'])->orWhere('user_id',auth()->id())->Where('friends_id',$request['user'])->delete();
        
        }
        return back();



    }

}
