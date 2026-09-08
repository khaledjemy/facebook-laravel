<?php

namespace Tests\Feature;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function createUser(string $email): User
    {
        return User::create([
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => $email,
            'password' => bcrypt('Secret123!'),
        ]);
    }

    public function test_reaction_triggers_notification_for_post_owner_only_if_not_author(): void
    {
        $owner = $this->createUser('owner@example.com');
        $actor = $this->createUser('actor@example.com');

        $post = Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Post to react to',
            'status' => 1,
        ]);

        // Actor reacts to post
        $this->actingAs($actor)
            ->postJson('/like', [
                'post_id' => $post->id,
                'type_id' => 2, // Love
                'liked' => false,
            ])
            ->assertOk();

        $this->assertCount(1, $owner->notifications);
        $notif = $owner->notifications->first();
        $this->assertSame('reaction', $notif->data['type']);
        $this->assertSame($actor->id, $notif->data['actor_id']);
        $this->assertSame($post->id, $notif->data['post_id']);

        // Owner reacts to their own post -> should not notify themselves
        $this->actingAs($owner)
            ->postJson('/like', [
                'post_id' => $post->id,
                'type_id' => 1,
                'liked' => false,
            ])
            ->assertOk();

        $this->assertCount(1, $owner->fresh()->notifications);
    }

    public function test_comment_triggers_notification_for_post_owner(): void
    {
        $owner = $this->createUser('owner-comment@example.com');
        $actor = $this->createUser('actor-comment@example.com');

        $post = Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Post for comment test',
            'status' => 1,
        ]);

        $this->actingAs($actor)
            ->postJson('/comment', [
                'post_id' => $post->id,
                'comment' => 'Great post!',
            ])
            ->assertOk();

        $this->assertCount(1, $owner->notifications);
        $notif = $owner->notifications->first();
        $this->assertSame('comment', $notif->data['type']);
        $this->assertSame($actor->id, $notif->data['actor_id']);
        $this->assertSame($post->id, $notif->data['post_id']);
    }

    public function test_friend_request_and_acceptance_trigger_notifications(): void
    {
        $userA = $this->createUser('friendA@example.com');
        $userB = $this->createUser('friendB@example.com');

        // userA sends request to userB
        $this->actingAs($userA)
            ->post('/f_action', [
                'user' => $userB->id,
                'type' => 'add',
            ])
            ->assertRedirect();

        $this->assertCount(1, $userB->notifications);
        $reqNotif = $userB->notifications->first();
        $this->assertSame('friend_request', $reqNotif->data['type']);
        $this->assertSame($userA->id, $reqNotif->data['actor_id']);

        // userB accepts request from userA
        $this->actingAs($userB)
            ->post('/f_action', [
                'user' => $userA->id,
                'type' => 'accept',
            ])
            ->assertRedirect();

        $this->assertCount(1, $userA->notifications);
        $accNotif = $userA->notifications->first();
        $this->assertSame('friend_accepted', $accNotif->data['type']);
        $this->assertSame($userB->id, $accNotif->data['actor_id']);
    }

    public function test_notifications_api_endpoints_work_correctly(): void
    {
        $owner = $this->createUser('notif-api@example.com');
        $actor = $this->createUser('notif-actor@example.com');

        $post = Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Notification API test post',
            'status' => 1,
        ]);

        $this->actingAs($actor)->postJson('/like', [
            'post_id' => $post->id,
            'type_id' => 1,
            'liked' => false,
        ]);

        // GET /notifications
        $response = $this->actingAs($owner)
            ->getJson('/notifications')
            ->assertOk()
            ->assertJsonStructure(['unread_count', 'notifications']);

        $this->assertSame(1, $response->json('unread_count'));
        $notifId = $response->json('notifications.0.id');

        // Mark single as read
        $this->actingAs($owner)
            ->postJson('/notifications/' . $notifId . '/read')
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertSame(0, $owner->fresh()->unreadNotifications()->count());

        // Create another notification
        $this->actingAs($actor)->postJson('/comment', [
            'post_id' => $post->id,
            'comment' => 'Another comment',
        ]);

        $this->assertSame(1, $owner->fresh()->unreadNotifications()->count());

        // Mark all as read
        $this->actingAs($owner)
            ->postJson('/notifications/read-all')
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertSame(0, $owner->fresh()->unreadNotifications()->count());
    }
}
