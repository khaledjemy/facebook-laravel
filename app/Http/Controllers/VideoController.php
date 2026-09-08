<?php

namespace App\Http\Controllers;

use App\Video ;
use App\Videocommente;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    //
 public function video($id)
    {

        $video  =   Video::where('id',$id)->with('user','videocommentes','videoreact')->firstOrFail();
        $commente=   Videocommente::where('video_id',$id)->with('user','reply')->get();
        if (auth()->check()) {
            $profilee = new UsersController;
            $profile = $profilee->profilenav();
        } else {
            $profile = null;
        }
       // dd($video);
        return view('/video',compact('video','profile'));


    }
}
