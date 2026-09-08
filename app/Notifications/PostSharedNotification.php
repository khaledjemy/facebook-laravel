<?php

namespace App\Notifications;

use App\Post;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostSharedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $actor,
        public Post $originalPost,
        public Post $sharedPost,
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $actorName = trim($this->actor->first_name . ' ' . $this->actor->last_name);

        return [
            'type' => 'share',
            'actor_id' => $this->actor->id,
            'actor_name' => $actorName,
            'actor_avatar' => $this->actor->avatar_url,
            'post_id' => $this->sharedPost->id,
            'original_post_id' => $this->originalPost->id,
            'title' => 'مشاركة منشور',
            'message' => 'قام بمشاركة منشورك على صفحته',
            'url' => url('/post/' . $this->sharedPost->id),
        ];
    }
}
