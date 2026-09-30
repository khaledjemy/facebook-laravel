<?php

namespace App\Http\Controllers;

use App\CommentReact;
use App\Notifications\NewReactionNotification;
use App\Post;
use App\React;
use App\PhotoReact;
use App\VideoReact;
use App\Video;
use App\Services\MediaVisibilityService;
use App\photo as Photo;
use App\Commente;
use Illuminate\Http\Request;

class ReactController extends Controller
{
    //
    public function react(Request $request)
    {
        $data = $request->validate(['post_id'=>'required|integer|exists:posts,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
        $postId = (int) $data['post_id'];
        Post::visibleTo(auth()->user())->whereKey($postId)->firstOrFail();
        $reactionType = (int) ($data['type_id'] ?? 1);

        if ($request->boolean('liked')) {
            React::where('post_id', $postId)->where('user_id', $userId)->delete();
            $liked = false;
        } else {
            React::updateOrCreate(
                ['post_id' => $postId, 'user_id' => $userId],
                ['type' => $reactionType]
            );
            $liked = true;

            $post = Post::with('user')->find($postId);
            if ($post && $post->user && (int) $post->user_id !== (int) $userId) {
                $post->user->notify(new NewReactionNotification(auth()->user(), $post, $reactionType));
            }
        }

        $reactionCounts = React::where('post_id', $postId)
            ->get(['type'])
            ->countBy(fn ($reaction) => (int) ($reaction->type ?? 1))
            ->sortDesc();

        return response()->json([
            'status' => 'ok',
            'media_type' => 'post',
            'media_id' => $postId,
            'liked' => $liked,
            'count' => $reactionCounts->sum(),
            'reaction_counts' => $reactionCounts,
            'reaction_type' => $liked ? $reactionType : null,
            'reaction_emoji' => $liked ? ([1=>'👍', 2=>'❤️', 3=>'🤗', 4=>'😂', 5=>'😮', 6=>'😢', 7=>'😡'][$reactionType] ?? '👍') : null,
        ]);
    }
    public function react_photo(Request $request, MediaVisibilityService $mediaVisibility)
    {
        $data = $request->validate(['photo_id'=>'required|integer|exists:photos,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
        $photoId = (int) $data['photo_id'];
        $photo = Photo::findOrFail($photoId);
        abort_unless($mediaVisibility->canViewPhoto($photo, auth()->user()), 404);
        $reactionType = (int) ($data['type_id'] ?? 1);

        if ($request->boolean('liked')) {
            PhotoReact::where('photo_id', $photoId)->where('user_id', $userId)->delete();
            $liked = false;
        } else {
            PhotoReact::updateOrCreate(
                ['photo_id' => $photoId, 'user_id' => $userId],
                ['type' => $reactionType]
            );
            $liked = true;
        }

        $reactionCounts = PhotoReact::where('photo_id', $photoId)
            ->get(['type'])
            ->countBy(fn ($reaction) => (int) ($reaction->type ?? 1))
            ->sortDesc();

        return response()->json([
            'status' => 'ok',
            'liked' => $liked,
            'count' => $reactionCounts->sum(),
            'reaction_counts' => $reactionCounts,
            'reaction_type' => $liked ? $reactionType : null,
            'reaction_emoji' => $liked ? ([1=>'👍', 2=>'❤️', 3=>'🤗', 4=>'😂', 5=>'😮', 6=>'😢', 7=>'😡'][$reactionType] ?? '👍') : null,
            'media_type' => 'photo',
            'media_id' => $photoId,
        ]);
    }

    public function react_video(Request $request, MediaVisibilityService $mediaVisibility)
    {
        $data = $request->validate([
            'video_id' => 'required|integer|exists:videos,id',
            'type_id' => 'nullable|integer|between:1,7',
            'liked' => 'nullable',
        ]);
        $userId = auth()->id();
        $video = Video::findOrFail($data['video_id']);
        abort_unless($mediaVisibility->canViewVideo($video, auth()->user()), 404);

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
            'reaction_emoji' => $liked ? ([1=>'👍',2=>'❤️',3=>'🤗',4=>'😂',5=>'😮',6=>'😢',7=>'😡'][$data['type_id'] ?? 1] ?? '👍') : null,
            'media_type' => 'video',
            'media_id' => (int) $data['video_id'],
        ]);
    }
    public function react_comment(Request $request)
    {
        $data = $request->validate(['comment_id'=>'required|integer|exists:commentes,id','type_id'=>'nullable|integer|between:1,7','liked'=>'nullable']);
        $userId = auth()->id();
        $comment = Commente::findOrFail($data['comment_id']);
        abort_unless(Post::visibleTo(auth()->user())->whereKey($comment->post_id)->exists(), 404);
 
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
        Post::visibleTo(auth()->user())->whereKey($post)->firstOrFail();
        $labels = [1 => 'Like', 2 => 'Love', 3 => 'Care', 4 => 'Haha', 5 => 'Wow', 6 => 'Sad', 7 => 'Angry'];
        $reactions = React::with('user.photopro')->where('post_id', $post)->latest()->get()->map(function ($reaction) use ($labels) {
            $photo = $reaction->user?->photopro;
            return [
                'name' => trim(($reaction->user->first_name ?? '') . ' ' . ($reaction->user->last_name ?? '')),
                'profile_url' => url('/profile/'.($reaction->user_id ?? 0)),
                'avatar' => $photo?->url ?? asset('img/Default_avatar_profile.jpg'),
                'type' => $labels[$reaction->type] ?? 'Like',
                'type_id' => (int) $reaction->type,
                'emoji' => [1=>'👍',2=>'❤️',3=>'🤗',4=>'😂',5=>'😮',6=>'😢',7=>'😡'][$reaction->type] ?? '👍',
            ];
        });
        return response()->json($reactions);
    }
}
