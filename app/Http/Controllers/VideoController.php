<?php

namespace App\Http\Controllers;

use App\Video ;
use App\Videocommente;
use App\Services\MediaVisibilityService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    //
 public function video($id, MediaVisibilityService $mediaVisibility)
    {

        $video  =   Video::where('id',$id)->with('user','videocommentes','videoreact')->firstOrFail();
        abort_unless($mediaVisibility->canViewVideo($video, auth()->user()), 404);
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
