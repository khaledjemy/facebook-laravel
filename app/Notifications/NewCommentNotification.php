<?php

namespace App\Notifications;

use App\Post;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewCommentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $actor,
        public Post $post,
        public string $commentText = '',
        public ?int $commentId = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $actorName = trim($this->actor->first_name . ' ' . $this->actor->last_name);
        $snippet = $this->commentText ? ': ' . Str::limit($this->commentText, 40) : '';

        return [
            'type' => 'comment',
            'actor_id' => $this->actor->id,
            'actor_name' => $actorName,
            'actor_avatar' => $this->actor->avatar_url,
            'post_id' => $this->post->id,
            'comment_id' => $this->commentId,
            'title' => 'تعليق جديد',
            'message' => "علق على منشورك{$snippet}",
            'url' => url('/post/' . $this->post->id),
        ];
    }
}
