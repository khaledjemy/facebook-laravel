<?php

namespace Tests\Feature;

use App\Post;
use App\React;
use App\SavedPost;
use App\User;
use App\Friend;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SocialFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function user(string $email='user@example.com'): User
    {
        return User::create(['first_name'=>'Test','last_name'=>'User','email'=>$email,'password'=>bcrypt('Secret123!')]);
    }

    public function test_private_pages_redirect_guests_to_login(): void
    {
        foreach(['/community','/friends','/saved','/memories','/messanger/1'] as $url) $this->get($url)->assertRedirect('/login');
    }

    public function test_user_can_save_and_unsave_a_post(): void
    {
        $user=$this->user(); $post=Post::create(['user_id'=>$user->id,'post_text'=>'Save me','status'=>1]);
        $this->actingAs($user)->postJson('/saved/'.$post->id)->assertOk()->assertJson(['saved'=>true]);
        $this->assertDatabaseHas('saved_posts',['user_id'=>$user->id,'post_id'=>$post->id]);
        $this->actingAs($user)->postJson('/saved/'.$post->id)->assertOk()->assertJson(['saved'=>false]);
        $this->assertDatabaseMissing('saved_posts',['user_id'=>$user->id,'post_id'=>$post->id]);
    }

    public function test_reaction_uses_authenticated_user_and_validates_type(): void
    {
        $user=$this->user(); $other=$this->user('other@example.com'); $post=Post::create(['user_id'=>$other->id,'post_text'=>'React','status'=>1]);
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>2,'liked'=>false,'user_id'=>$other->id])->assertOk();
        $this->assertDatabaseHas('reacts',['post_id'=>$post->id,'user_id'=>$user->id,'type'=>2]);
        $this->getJson('/post/'.$post->id.'/reactions')->assertOk()->assertJsonFragment([
            'name' => 'Test User', 'type' => 'Love', 'type_id' => 2, 'emoji' => '❤️',
        ]);
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>99,'liked'=>false])->assertUnprocessable();
    }

    public function test_post_visibility_is_enforced_for_owner_friends_and_others(): void
    {
        $owner = $this->user('owner@example.com');
        $friend = $this->user('friend@example.com');
        $other = $this->user('stranger@example.com');
        Friend::forceCreate(['user_id' => $owner->id, 'friends_id' => $friend->id, 'state' => true]);

        $public = Post::create(['user_id' => $owner->id, 'post_text' => 'Public post', 'status' => 1, 'visibility' => 'public']);
        $friends = Post::create(['user_id' => $owner->id, 'post_text' => 'Friends post', 'status' => 1, 'visibility' => 'friends']);
        $private = Post::create(['user_id' => $owner->id, 'post_text' => 'Private post', 'status' => 1, 'visibility' => 'only_me']);

        $this->actingAs($owner)->get('/post/'.$private->id)->assertOk();
        $this->actingAs($friend)->get('/post/'.$friends->id)->assertOk();
        $this->actingAs($friend)->get('/post/'.$private->id)->assertNotFound();
        $this->actingAs($other)->get('/post/'.$friends->id)->assertNotFound();
        $this->actingAs($other)->get('/post/'.$public->id)->assertOk();
    }

    public function test_comments_and_replies_accept_image_or_video_attachments(): void
    {
        Queue::fake();
        $user = $this->user('media-comment@example.com');
        $post = Post::create(['user_id' => $user->id, 'post_text' => 'Media comments', 'status' => 1]);

        try {
            $commentResponse = $this->actingAs($user)->postJson('/comment', [
                'post_id' => $post->id,
                'media' => UploadedFile::fake()->createWithContent('comment.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
            ])->assertOk()->assertJson(['status' => 'ok']);

            $commentId = $commentResponse->json('details.id');
            $this->assertDatabaseHas('commentes', [
                'id' => $commentId, 'post_id' => $post->id, 'media_type' => 'image',
            ]);

            $this->actingAs($user)->postJson('/post-reply', [
                'comment_id' => $commentId,
                'userreplay_id' => $user->id,
                'media' => UploadedFile::fake()->create('reply.mp4', 100, 'video/mp4'),
            ])->assertOk()->assertJson(['status' => 'ok']);

            $this->assertDatabaseHas('replies', [
                'comment_id' => $commentId, 'media_type' => 'video',
            ]);
            Queue::assertPushed(\App\Jobs\ProcessVideoJob::class);
        } finally {
            File::deleteDirectory(public_path('comment-media/'.$user->id));
        }
    }

    public function test_language_switch_is_limited_to_supported_locales(): void
    {
        $this->get('/language/ar')->assertRedirect();
        $this->assertSame('ar',session('locale'));
        $this->get('/language/fr')->assertNotFound();
    }

    public function test_authenticated_main_pages_render(): void
    {
        $this->seed();
        $demo = User::where('email', 'demo@example.com')->firstOrFail();

        $pages = [
            '/', '/profile/'.$demo->id, '/post/1', '/messanger/2',
            '/community', '/pages', '/groups', '/watch', '/friends',
            '/saved', '/memories', '/search?q=Demo',
        ];

        foreach ($pages as $page) {
            $this->actingAs($demo)->get($page)->assertOk();
        }
    }

    public function test_profile_renders_comments_and_replies_when_users_have_no_avatar(): void
    {
        $owner = $this->user('no-avatar-owner@example.com');
        $commenter = $this->user('no-avatar-commenter@example.com');
        $post = Post::create(['user_id' => $owner->id, 'post_text' => 'No avatars', 'status' => 1]);
        $comment = \App\Commente::create(['post_id' => $post->id, 'user_id' => $commenter->id, 'text_co' => 'Comment']);
        \App\Replie::create([
            'comment_id' => $comment->id,
            'user_id' => $owner->id,
            'userreply_id' => $commenter->id,
            'reply' => 'Reply',
        ]);

        $this->actingAs($owner)
            ->get('/profile/'.$owner->id)
            ->assertOk()
            ->assertSee('img/Default_avatar_profile.jpg', false);
    }

    public function test_authenticated_user_can_load_a_safe_profile_hover_card(): void
    {
        $viewer = $this->user('hover-viewer@example.com');
        $person = $this->user('hover-person@example.com');

        $this->actingAs($viewer)
            ->getJson('/users/'.$person->id.'/hover-card')
            ->assertOk()
            ->assertJsonPath('id', $person->id)
            ->assertJsonPath('name', 'Test User')
            ->assertJsonPath('friend_status', 'none')
            ->assertJsonMissingPath('email')
            ->assertJsonStructure(['avatar', 'about', 'mutual_count', 'mutual_names', 'profile_url', 'message_url']);
    }

    public function test_websocket_ticket_is_authenticated_and_signed(): void
    {
        $this->getJson('/websocket-ticket')->assertUnauthorized();
        $user = $this->user();
        $ticket = $this->actingAs($user)->getJson('/websocket-ticket')->assertOk()->json();

        $this->assertSame((string) $user->id, $ticket['user_id']);
        $this->assertGreaterThan(time(), $ticket['expires']);
        $this->assertSame(
            hash_hmac('sha256', $ticket['user_id'].'|'.$ticket['expires'], (string) config('services.websocket_secret')),
            $ticket['signature']
        );
    }

    public function test_registration_requires_matching_password_and_unique_email(): void
    {
        $existing = $this->user('taken@example.com');

        $this->post('/regi', [
            'firstname' => 'New', 'lastname' => 'User', 'emailsignup' => $existing->email,
            'passwordsignup' => 'Password123!', 'passwordsignup_confirm' => 'Different123!',
        ])->assertSessionHasErrors(['emailsignup', 'passwordsignup_confirm']);

        $this->post('/regi', [
            'firstname' => 'New', 'lastname' => 'User', 'emailsignup' => 'NEW@example.com',
            'passwordsignup' => 'Password123!', 'passwordsignup_confirm' => 'Password123!',
        ])->assertRedirect();

        $created = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('Password123!', $created->password));
    }

    public function test_mixed_media_upload_uses_safe_extensions(): void
    {
        Queue::fake();
        $user = User::forceCreate([
            'id' => 999, 'first_name' => 'Upload', 'last_name' => 'Tester',
            'email' => 'upload@example.com', 'password' => bcrypt('Password123!'),
        ]);
        $imageTemp = tempnam(sys_get_temp_dir(), 'social-image-');
        $videoTemp = tempnam(sys_get_temp_dir(), 'social-video-');
        file_put_contents($imageTemp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        file_put_contents($videoTemp, hex2bin('000000186674797069736F6D0000020069736F6D69736F32'));

        try {
            $this->actingAs($user)->post('/posts', [
                'post_text' => 'Mixed upload',
                'files' => [
                    new UploadedFile($imageTemp, 'avatar.png', null, null, true),
                    new UploadedFile($videoTemp, 'clip.mp4', null, null, true),
                ],
            ])->assertOk();

            $this->assertDatabaseHas('photos', ['user_id' => 999, 'type' => '.png']);
            $this->assertDatabaseHas('videos', ['user_id' => 999, 'type' => '.mp4']);
        } finally {
            File::deleteDirectory(public_path('images/users/999'));
            File::deleteDirectory(public_path('video/users/999'));
            if (is_file($imageTemp)) unlink($imageTemp);
            if (is_file($videoTemp)) unlink($videoTemp);
        }
    }

    public function test_mkv_video_can_be_added_to_a_post_and_is_queued_for_processing(): void
    {
        Queue::fake();
        $user = User::forceCreate([
            'id' => 998,
            'first_name' => 'Mkv',
            'last_name' => 'Tester',
            'email' => 'mkv@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        try {
            $this->actingAs($user)->postJson('/posts', [
                'post_text' => 'MKV upload',
                'files' => [UploadedFile::fake()->create('clip.mkv', 100, 'video/x-matroska')],
            ])->assertOk()->assertJson(['success' => 'ok']);

            $this->assertDatabaseHas('videos', ['user_id' => 998, 'type' => '.mkv']);
            Queue::assertPushed(\App\Jobs\ProcessVideoJob::class);
        } finally {
            File::deleteDirectory(public_path('video/users/998'));
        }
    }

    public function test_user_can_update_profile_and_cover_images(): void
    {
        $user = User::forceCreate([
            'id' => 997,
            'first_name' => 'Profile',
            'last_name' => 'Tester',
            'email' => 'profile_test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        try {
            $profile = $this->actingAs($user)->postJson('/make-profile-picture', [
                'files' => UploadedFile::fake()->createWithContent('profile.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
                'cover' => false,
            ])->assertOk()->assertJsonStructure(['profile']);

            $cover = $this->actingAs($user)->postJson('/make-profile-picture', [
                'files' => UploadedFile::fake()->createWithContent('cover.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
                'cover' => true,
            ])->assertOk()->assertJsonStructure(['cover']);

            $user->refresh();
            $this->assertNotNull($user->profile_photo_id);
            $this->assertNotNull($user->cover_photo_id);
            $this->assertNotSame($user->profile_photo_id, $user->cover_photo_id);
        } finally {
            File::deleteDirectory(public_path('images/users/997'));
        }
    }
}
