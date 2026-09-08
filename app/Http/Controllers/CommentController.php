<?php

namespace App\Http\Controllers;

use App\Commente;
use App\Notifications\NewCommentNotification;
use App\Photocommente;
use App\Videocommente;
use App\photoreply;
use App\Post;
use App\Replie;
use App\Jobs\ProcessVideoJob;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            
            'comment' => 'nullable|required_without:media|max:255',
            'post_id' => 'required|integer|exists:posts,id',
            'media' => 'nullable|file|max:51200|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,video/x-matroska,video/avi,video/x-msvideo,video/mpeg,video/3gpp',

        ]);
        $userId =   auth()->id();
        $media = $this->storeCommentMedia($request);
        $comment = Commente::create([
            'text_co' => $validatedData['comment'] ?? '',
            'user_id'   => $userId, 
            'post_id'     => $validatedData['post_id'],
            'media_path' => $media['path'],
            'media_type' => $media['type'],
        ]);

        $this->processVideoMedia($comment, $media);

        $post = Post::with('user')->find($validatedData['post_id']);
        if ($post && $post->user && (int) $post->user_id !== (int) $userId) {
            $post->user->notify(new NewCommentNotification(auth()->user(), $post, $validatedData['comment'] ?? '', $comment->id));
        }
        

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

    public function videocommentstore(Request $request)
    {
        $data = $request->validate([
            'comment' => 'required|string|max:255',
            'video_id' => 'required|integer|exists:videos,id',
        ]);

        $comment = Videocommente::create([
            'comment' => trim($data['comment']),
            'user_id' => auth()->id(),
            'video_id' => $data['video_id'],
        ]);
        $comment->load('user.photopro');

        return response()->json([
            'status' => 'ok',
            'details' => $comment,
            'count' => Videocommente::where('video_id', $data['video_id'])->count(),
        ]);
    }
    
    public function reply_store(Request $request)
    {   
        
        
        $validatedData = $request->validate([
            
            'comment' => 'nullable|required_without:media|max:255',
            'comment_id' => 'required|integer|exists:commentes,id',
            'userreplay_id' => 'required|integer|exists:users,id',
            'media' => 'nullable|file|max:51200|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime,video/x-matroska,video/avi,video/x-msvideo,video/mpeg,video/3gpp',

        ]);
        
        $userId =   auth()->id();
        $media = $this->storeCommentMedia($request);
        $reply = Replie::create([
            
            'comment_id'    => $validatedData['comment_id'],
            'user_id'       => $userId, // Assign the temporary user id
            'userreply_id'  => $validatedData['userreplay_id'],
            'reply'         => $validatedData['comment'] ?? '',
            'media_path'    => $media['path'],
            'media_type'    => $media['type'],
        ]);

        $this->processVideoMedia($reply, $media);
        

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

    private function storeCommentMedia(Request $request): array
    {
        if (!$request->hasFile('media')) {
            return ['path' => null, 'type' => null];
        }

        $file = $request->file('media');
        $type = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
        $extension = strtolower($file->guessExtension() ?: ($type === 'video' ? 'mp4' : 'jpg'));
        $name = bin2hex(random_bytes(16)).'.'.$extension;
        $relativeDirectory = 'comment-media/'.auth()->id();
        $file->move(public_path($relativeDirectory), $name);

        $path = $relativeDirectory.'/'.$name;
        return ['path' => $path, 'type' => $type, 'source_path' => $path];
    }

    private function processVideoMedia($model, array $media): void
    {
        if (($media['type'] ?? null) !== 'video') {
            return;
        }

        $directory = 'comment-media/'.auth()->id().'/'.$model->getTable().'-'.$model->id;
        ProcessVideoJob::dispatch(
            $media['source_path'],
            $directory,
            $model::class,
            $model->id,
            'media_path',
        );
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
