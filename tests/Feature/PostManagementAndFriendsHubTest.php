<?php

namespace Tests\Feature;

use App\Friend;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PostManagementAndFriendsHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_own_post_text_and_visibility()
    {
        $user = User::create([
            'first_name' => 'Post',
            'last_name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'post_text' => 'Original Text',
            'visibility' => 'public',
            'status' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/post/' . $post->id . '/update', [
            'post_text' => 'Updated Text by Owner',
            'visibility' => 'friends',
        ]);

        $response->assertJson([
            'status' => true,
            'message' => 'تم تحديث المنشور بنجاح.',
        ]);

        $post->refresh();
        $this->assertEquals('Updated Text by Owner', $post->post_text);
        $this->assertEquals('friends', $post->visibility);
    }

    public function test_user_cannot_update_another_users_post()
    {
        $user1 = User::create([
            'first_name' => 'Owner',
            'last_name' => 'One',
            'email' => 'owner1@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $user2 = User::create([
            'first_name' => 'Hacker',
            'last_name' => 'Two',
            'email' => 'hacker2@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $post = Post::create([
            'user_id' => $user1->id,
            'post_text' => 'Original Text',
            'visibility' => 'public',
            'status' => true,
        ]);

        $response = $this->actingAs($user2)->postJson('/post/' . $post->id . '/update', [
            'post_text' => 'Hacked Text',
            'visibility' => 'only_me',
        ]);

        $response->assertStatus(403);
        $post->refresh();
        $this->assertEquals('Original Text', $post->post_text);
    }

    public function test_user_can_delete_own_post()
    {
        $user = User::create([
            'first_name' => 'Deleter',
            'last_name' => 'User',
            'email' => 'deleter@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'post_text' => 'To Be Deleted',
            'visibility' => 'public',
            'status' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/postdelete', [
            'id' => $post->id,
        ]);

        $response->assertJson([
            'success' => 'Post deleted successfully',
        ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_user_cannot_delete_another_users_post()
    {
        $user1 = User::create([
            'first_name' => 'Real',
            'last_name' => 'Owner',
            'email' => 'real@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $user2 = User::create([
            'first_name' => 'Other',
            'last_name' => 'User',
            'email' => 'other@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $post = Post::create([
            'user_id' => $user1->id,
            'post_text' => 'Protected Post',
            'visibility' => 'public',
            'status' => true,
        ]);

        $response = $this->actingAs($user2)->postJson('/postdelete', [
            'id' => $post->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_friends_hub_renders_incoming_requests_and_friends()
    {
        $me = User::create([
            'first_name' => 'Main',
            'last_name' => 'User',
            'email' => 'main@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $requester = User::create([
            'first_name' => 'Pending',
            'last_name' => 'Requester',
            'email' => 'requester@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $confirmed = User::create([
            'first_name' => 'Confirmed',
            'last_name' => 'Friend',
            'email' => 'confirmed@example.com',
            'password' => Hash::make('secret123'),
        ]);

        // Incoming request to me
        Friend::create([
            'user_id' => $requester->id,
            'friends_id' => $me->id,
            'state' => 0,
        ]);

        // Confirmed friend
        Friend::create([
            'user_id' => $me->id,
            'friends_id' => $confirmed->id,
            'state' => 1,
        ]);

        $response = $this->actingAs($me)->get('/friends');
        $response->assertStatus(200);
        $response->assertSee('طلبات الصداقة المعلقة');
        $response->assertSee('Pending Requester');
        $response->assertSee('Confirmed Friend');
    }

    public function test_user_can_accept_friend_request_via_ajax()
    {
        $me = User::create([
            'first_name' => 'Acceptor',
            'last_name' => 'User',
            'email' => 'acceptor@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $sender = User::create([
            'first_name' => 'Sender',
            'last_name' => 'User',
            'email' => 'sender@example.com',
            'password' => Hash::make('secret123'),
        ]);

        Friend::create([
            'user_id' => $sender->id,
            'friends_id' => $me->id,
            'state' => 0,
        ]);

        $response = $this->actingAs($me)->postJson('/f_action', [
            'user' => $sender->id,
            'type' => 'accept',
        ]);

        $response->assertJson([
            'status' => true,
            'type' => 'accept',
        ]);

        $this->assertDatabaseHas('friends', [
            'user_id' => $sender->id,
            'friends_id' => $me->id,
            'state' => 1,
        ]);
    }

    public function test_user_can_send_friend_request_via_ajax()
    {
        $me = User::create([
            'first_name' => 'Requester',
            'last_name' => 'User',
            'email' => 'req@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $target = User::create([
            'first_name' => 'Target',
            'last_name' => 'User',
            'email' => 'target@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($me)->postJson('/f_action', [
            'user' => $target->id,
            'type' => 'add',
        ]);

        $response->assertJson([
            'status' => true,
            'type' => 'add',
        ]);

        $this->assertDatabaseHas('friends', [
            'user_id' => $me->id,
            'friends_id' => $target->id,
            'state' => 0,
        ]);
    }

    public function test_user_can_remove_friend_via_ajax()
    {
        $me = User::create([
            'first_name' => 'Remover',
            'last_name' => 'User',
            'email' => 'remover@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $friend = User::create([
            'first_name' => 'Old',
            'last_name' => 'Friend',
            'email' => 'oldfriend@example.com',
            'password' => Hash::make('secret123'),
        ]);

        Friend::create([
            'user_id' => $me->id,
            'friends_id' => $friend->id,
            'state' => 1,
        ]);

        $response = $this->actingAs($me)->postJson('/f_action', [
            'user' => $friend->id,
            'type' => 'remove',
        ]);

        $response->assertJson([
            'status' => true,
            'type' => 'remove',
        ]);

        $this->assertDatabaseMissing('friends', [
            'user_id' => $me->id,
            'friends_id' => $friend->id,
        ]);
    }
}
