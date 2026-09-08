<?php

namespace App\Http\Controllers;

use App\Commente;
use App\Photocommente;
use App\photoreply;
use App\Replie;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            
            'comment' => 'required|max:255',
            'post_id' => 'required|integer|exists:posts,id',

        ]);
        $userId =   auth()->id();
        $comment = Commente::create([
            'text_co' => $validatedData['comment'],
            'user_id'   => $userId, 
            'post_id'     => $validatedData['post_id'],
        ]);
        

        if ($comment) {
           // $comment->load('user'); 
            $comment->load('user.photopro');       
            return response()->json([
                'status'  => 'ok',
                'details' => $comment
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'details' => 'Error'
            ]);
        } 
    }
    public function photocommentstore(Request $request)
    {
        
        $validatedData = $request->validate([
            
            'comment' => 'required|max:255',
            'photo_id' => 'required|integer|exists:photos,id'

        ]);
        $userId =   auth()->id();

        
        $comment = Photocommente::create([
            'comment' => $validatedData['comment'],
            'user_id'   => $userId, 
            'photo_id'     => $validatedData['photo_id']
        ]);
        if ($comment) {
            // $comment->load('user'); 
             $comment->load('user.photopro');       
             return response()->json([
                 'status'  => 'ok',
                 'details' => $comment
             ]);
         } else {
             return response()->json([
                 'status'  => 'error',
                 'details' => 'Error'
             ]);
         } 

       

    }
    
    public function reply_store(Request $request)
    {   
        
        
        $validatedData = $request->validate([
            
            'comment' => 'required|max:255',
            'comment_id' => 'required|integer|exists:commentes,id',
            'userreplay_id' => 'required|integer|exists:users,id',

        ]);
        
        $userId =   auth()->id();
        $reply = Replie::create([
            
            'comment_id'    => $validatedData['comment_id'],
            'user_id'       => $userId, // Assign the temporary user id
            'userreply_id'  => $validatedData['userreplay_id'],
            'reply'         => $validatedData['comment'],
        ]);
        

        if ($reply) {
             $reply->load('userreply'); 
             $reply->load('userreply.photopro');       
             return response()->json([
                 'status'  => 'ok',
                 'details' => $reply
             ]);
         } else {
             return response()->json([
                 'status'  => 'error',
                 'details' => 'Error'
             ]);
         } 
    }
    
    public function photo_reply_store(Request $request)
    {   
        
        
        $validatedData = $request->validate([
            
            'comment' => 'required|max:255',
            'comment_id' => 'required|integer|exists:photocommentes,id',
            'userreplay_id' => 'required|integer|exists:users,id',

        ]);
        
        $userId =   auth()->id();

        
        $reply = photoreply::create([
            
            'comment_id'    => $validatedData['comment_id'],
            'user_id'       => $userId, // Assign the temporary user id
            'userreply_id'  => $validatedData['userreplay_id'],
            'reply'         => $validatedData['comment'],
        ]);
        

       return redirect('/')->with('"success"', 'Comment successfully!');
    }
    public function delete_comment(Request  $request)
    {
        $validatedData = $request->validate([
            'comment_id' => 'required|integer|exists:commentes,id',
        ]);
        $userId =   auth()->id();
        $comment = Commente::where('id', $validatedData['comment_id'])->where('user_id', $userId)->first();
        if ($comment) {
            $comment->delete();
            return response()->json([
                'status'  => 'ok',
                'details' => $comment
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'details' => 'Comment not found or you do not have permission to delete it'
            ]);
        }
    }
    public function delete_reply(Request  $request)
    {
        $validatedData = $request->validate([
            'reply_id' => 'required|integer|exists:replies,id',
        ]);
        $userId =   auth()->id();
        $reply = Replie::where('id', $validatedData['reply_id'])->where('user_id', $userId)->first();
        if ($reply) {
            $reply->delete();
            return response()->json([
                'status'  => 'ok',
                'details' => 'Reply deleted successfully'
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'details' => 'Reply not found or you do not have permission to delete it'
            ]);
        }
    }
    public function delete_photo_reply(Request  $request)
    {
        $validatedData = $request->validate([
            'reply_id' => 'required|integer|exists:photoreplies,id',
        ]);
        $userId =   auth()->id();
        $reply = photoreply::where('id', $validatedData['reply_id'])->where('user_id', $userId)->first();
        if ($reply) {
            $reply->delete();
            return response()->json([
                'status'  => 'ok',
                'details' => 'Reply deleted successfully'
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'details' => 'Reply not found or you do not have permission to delete it'
            ]);
        }
    }
    public function delete_photocomment(Request  $request)
    {
        $validatedData = $request->validate([
            'comment_id' => 'required|integer|exists:photocommentes,id',
        ]);
        $userId =   auth()->id();
        $comment = Photocommente::where('id', $validatedData['comment_id'])->where('user_id', $userId)->first();;
        if ($comment) {
            $comment->delete();
            return response()->json([
                'status'  => 'ok',
                'details' => $comment
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'details' => 'Comment not found or you do not have permission to delete it'
            ]);
        }
    }


}
