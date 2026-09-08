<?php

namespace Tests\Feature;

use App\Block;
use App\Friend;
use App\Messanger;
use App\Notifications\PostSharedNotification;
use App\Photo;
use App\Post;
use App\Story;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PhaseThreeFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(string $email, string $firstName = 'Test', string $lastName = 'User'): User
    {
        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_user_can_create_and_fetch_and_delete_stories(): void
    {
        $user = $this->makeUser('story_user@test.com', 'Story', 'Creator');
        $otherUser = $this->makeUser('other_user@test.com', 'Other', 'User');

        $this->actingAs($user);

        // 1. Create text story
        $textRes = $this->postJson('/stories', [
            'type' => 'text',
            'content' => 'Hello World Story!',
            'background' => 'linear-gradient(135deg, #4f46e5, #06b6d4)',
        ]);
        $textRes->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonPath('story.content', 'Hello World Story!');

        $textStoryId = $textRes->json('story.id');

        // 2. Create image story
        $fakeImg = UploadedFile::fake()->createWithContent('story_photo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        $imgRes = $this->post('/stories', [
            'type' => 'image',
            'media' => $fakeImg,
            'content' => 'A photo caption',
        ]);
        $imgRes->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonPath('story.type', 'image');

        $imgStoryId = $imgRes->json('story.id');

        // Verify expiration is set in the future (~24 hours)
        $story = Story::findOrFail($textStoryId);
        $this->assertTrue($story->expires_at->isFuture());

        // 3. Fetch stories
        $getRes = $this->getJson('/stories');
        $getRes->assertStatus(200)
            ->assertJsonPath('status', true);
        $this->assertCount(1, $getRes->json('data')); // 1 user group
        $this->assertCount(2, $getRes->json('data.0.stories'));

        // 4. Other user cannot delete this story
        $this->actingAs($otherUser);
        $delForbidden = $this->deleteJson('/stories/' . $textStoryId);
        $delForbidden->assertStatus(403);

        // 5. Author can delete story
        $this->actingAs($user);
        $delRes = $this->deleteJson('/stories/' . $textStoryId);
        $delRes->assertStatus(200)->assertJsonPath('status', true);
        $this->assertDatabaseMissing('stories', ['id' => $textStoryId]);

        // 6. Expired story is not returned in active list
        $expiredStory = Story::create([
            'user_id' => $user->id,
            'type' => 'text',
            'content' => 'Old story',
            'expires_at' => now()->subHour(),
        ]);
        $getRes2 = $this->getJson('/stories');
        $allIds = collect($getRes2->json('data'))->flatMap(fn ($u) => collect($u['stories'])->pluck('id'));
        $this->assertFalse($allIds->contains($expiredStory->id));
    }

    public function test_user_can_share_post_and_author_receives_notification(): void
    {
        Notification::fake();

        $author = $this->makeUser('author@test.com', 'Post', 'Author');
        $sharer = $this->makeUser('sharer@test.com', 'Post', 'Sharer');

        $originalPost = Post::create([
            'user_id' => $author->id,
            'post_text' => 'Original interesting post content!',
            'status' => 1,
            'visibility' => 'public',
        ]);

        $this->actingAs($sharer);

        $shareRes = $this->postJson('/post/' . $originalPost->id . '/share', [
            'post_text' => 'Sharing this awesome post!',
            'visibility' => 'public',
        ]);

        $shareRes->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('post.shared_post_id', $originalPost->id)
            ->assertJsonPath('post.post_text', 'Sharing this awesome post!');

        $this->assertDatabaseHas('posts', [
            'user_id' => $sharer->id,
            'shared_post_id' => $originalPost->id,
            'post_text' => 'Sharing this awesome post!',
        ]);

        // Assert notification sent to original author
        Notification::assertSentTo(
            $author,
            PostSharedNotification::class,
            function (PostSharedNotification $notification) use ($author, $sharer, $originalPost) {
                return (int) $notification->actor->id === (int) $sharer->id &&
                       (int) $notification->originalPost->id === (int) $originalPost->id;
            }
        );
    }

    public function test_messenger_attachment_upload_and_storage(): void
    {
        $sender = $this->makeUser('sender@test.com', 'Alice', 'Sender');
        $receiver = $this->makeUser('receiver@test.com', 'Bob', 'Receiver');

        $this->actingAs($sender);

        $fakeFile = UploadedFile::fake()->createWithContent('chat_img.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));

        $response = $this->post('/messanger/' . $receiver->id, [
            'text' => 'Check out this picture!',
            'attachment' => $fakeFile,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('message', 'Check out this picture!')
            ->assertJsonPath('attachment_type', 'image');

        $msgId = $response->json('id');
        $msg = Messanger::findOrFail($msgId);

        $this->assertSame('image', $msg->attachment_type);
        $this->assertNotNull($msg->attachment);
        $this->assertStringContainsString('chat_attachments', $msg->attachment);
    }

    public function test_user_blocking_breaks_friendship_and_blocks_interactions(): void
    {
        $userA = $this->makeUser('usera@test.com', 'Alice', 'Blocker');
        $userB = $this->makeUser('userb@test.com', 'Bob', 'Blocked');

        // Establish friendship
        Friend::create(['user_id' => $userA->id, 'friends_id' => $userB->id, 'state' => 1]);

        $this->actingAs($userA);

        // 1. User A blocks User B
        $blockRes = $this->postJson('/users/' . $userB->id . '/block');
        $blockRes->assertStatus(200)->assertJsonPath('status', true);

        $this->assertDatabaseHas('blocks', [
            'user_id' => $userA->id,
            'blocked_id' => $userB->id,
        ]);

        // Friendship should be destroyed automatically
        $this->assertDatabaseMissing('friends', [
            'user_id' => $userA->id,
            'friends_id' => $userB->id,
        ]);

        // 2. User B cannot send a friend request to User A
        $this->actingAs($userB);
        $friendReqRes = $this->post('/f_action', [
            'user' => $userA->id,
            'type' => 'add',
        ]);
        $friendReqRes->assertStatus(403);

        // 3. User B cannot view User A's profile
        $profileRes = $this->get('/profile/' . $userA->id);
        $profileRes->assertStatus(404);

        // 4. User B cannot message User A
        $msgRes = $this->get('/messanger/' . $userA->id);
        $msgRes->assertStatus(403);

        // 5. User A's posts are hidden from User B in scopeVisibleTo
        $postA = Post::create([
            'user_id' => $userA->id,
            'post_text' => 'Secret post by A',
            'status' => 1,
            'visibility' => 'public',
        ]);

        $visibleToB = Post::visibleTo($userB)->pluck('id');
        $this->assertFalse($visibleToB->contains($postA->id));

        // 6. User A unblocks User B
        $this->actingAs($userA);
        $unblockRes = $this->postJson('/users/' . $userB->id . '/unblock');
        $unblockRes->assertStatus(200)->assertJsonPath('status', true);

        $this->assertDatabaseMissing('blocks', [
            'user_id' => $userA->id,
            'blocked_id' => $userB->id,
        ]);
    }
}
