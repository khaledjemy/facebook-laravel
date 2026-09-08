<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $actor,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $actorName = trim($this->actor->first_name . ' ' . $this->actor->last_name);

        return [
            'type' => 'friend_accepted',
            'actor_id' => $this->actor->id,
            'actor_name' => $actorName,
            'actor_avatar' => $this->actor->avatar_url,
            'title' => 'تم قبول الصداقة',
            'message' => 'وافق على طلب صداقتك',
            'url' => url('/profile/' . $this->actor->id),
        ];
    }
}
