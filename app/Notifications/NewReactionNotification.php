<?php

namespace App\Notifications;

use App\Post;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReactionNotification extends Notification
{
    use Queueable;

    protected string $emoji;
    protected string $label;

    public function __construct(
        public User $actor,
        public Post $post,
        public int $reactionType = 1,
    ) {
        $emojis = [1 => '👍', 2 => '❤️', 3 => '🤗', 4 => '😂', 5 => '😮', 6 => '😢', 7 => '😡'];
        $labels = [1 => 'Like', 2 => 'Love', 3 => 'Care', 4 => 'Haha', 5 => 'Wow', 6 => 'Sad', 7 => 'Angry'];

        $this->emoji = $emojis[$this->reactionType] ?? '👍';
        $this->label = $labels[$this->reactionType] ?? 'Like';
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $actorName = trim($this->actor->first_name . ' ' . $this->actor->last_name);

        return [
            'type' => 'reaction',
            'actor_id' => $this->actor->id,
            'actor_name' => $actorName,
            'actor_avatar' => $this->actor->avatar_url,
            'post_id' => $this->post->id,
            'reaction_type' => $this->reactionType,
            'emoji' => $this->emoji,
            'title' => 'تفاعل جديد',
            'message' => "تفاعل {$this->emoji} مع منشورك",
            'url' => url('/post/' . $this->post->id),
        ];
    }
}
