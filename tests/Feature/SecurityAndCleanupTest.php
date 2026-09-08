<?php

namespace Tests\Feature;

use App\Friend;
use App\Messanger;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndCleanupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function createUser(string $email = 'security@example.com'): User
    {
        return User::create([
            'first_name' => 'Sec',
            'last_name' => 'User',
            'email' => $email,
            'password' => bcrypt('Secret123!'),
        ]);
    }

    public function test_post_deletion_authorization(): void
    {
        $owner = $this->createUser('post-owner@example.com');
        $other = $this->createUser('post-other@example.com');

        $post = Post::create([
            'user_id' => $owner->id,
            'post_text' => 'My protected post',
            'status' => 1,
        ]);

        // Attempt delete as unauthorized user
        $this->actingAs($other)
            ->postJson('/postdelete', ['id' => $post->id])
            ->assertStatus(403);

        $this->assertDatabaseHas('posts', ['id' => $post->id]);

        // Attempt delete as owner
        $this->actingAs($owner)
            ->postJson('/postdelete', ['id' => $post->id])
            ->assertOk()
            ->assertJson(['success' => 'Post deleted successfully']);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_friend_action_prevents_self_friending_and_validates(): void
    {
        $user = $this->createUser('self@example.com');
        $other = $this->createUser('other@example.com');

        $this->actingAs($user)
            ->post('/f_action', ['user' => $user->id, 'type' => 'add'])
            ->assertStatus(422);

        $this->actingAs($user)
            ->post('/f_action', ['user' => $other->id, 'type' => 'invalid_type'])
            ->assertSessionHasErrors(['type']);

        $this->actingAs($user)
            ->post('/f_action', ['user' => $other->id, 'type' => 'add'])
            ->assertRedirect();

        $this->assertDatabaseHas('friends', [
            'user_id' => $user->id,
            'friends_id' => $other->id,
            'state' => 0,
        ]);
    }

    public function test_long_messenger_messages_up_to_2000_chars_are_stored(): void
    {
        $userA = $this->createUser('sender@example.com');
        $userB = $this->createUser('receiver@example.com');

        $longText = str_repeat('A long message content text ', 20); // ~560 characters
        $this->assertGreaterThan(255, mb_strlen($longText));

        $this->actingAs($userA)
            ->postJson('/messanger/'.$userB->id, ['text' => $longText])
            ->assertOk()
            ->assertJson(['status' => true]);

        $this->assertDatabaseHas('messangers', [
            'my_id' => $userA->id,
            'user_id' => $userB->id,
            'message' => trim($longText),
        ]);
    }

    public function test_messenger_inbox_redirects_to_latest_conversation(): void
    {
        $userA = $this->createUser('userA@example.com');
        $userB = $this->createUser('userB@example.com');
        $userC = $this->createUser('userC@example.com');

        Messanger::create([
            'my_id' => $userC->id,
            'user_id' => $userA->id,
            'message' => 'Latest message from C',
            'read' => 0,
        ]);

        $this->actingAs($userA)
            ->get('/messanger')
            ->assertRedirect('/messanger/'.$userC->id);
    }

    public function test_messenger_supports_full_page_and_mini_chat_responses(): void
    {
        $sender = $this->createUser('mini-sender@example.com');
        $receiver = $this->createUser('mini-receiver@example.com');

        Messanger::create([
            'my_id' => $sender->id,
            'user_id' => $receiver->id,
            'message' => 'Mini chat message',
            'read' => 0,
        ]);

        $this->actingAs($sender)
            ->get('/messanger/'.$receiver->id)
            ->assertOk()
            ->assertViewIs('messanger');

        $this->actingAs($sender)
            ->getJson('/messanger/'.$receiver->id.'?format=json')
            ->assertOk()
            ->assertJsonPath('0.message', 'Mini chat message')
            ->assertJsonPath('0.my_id', $sender->id);
    }
}
