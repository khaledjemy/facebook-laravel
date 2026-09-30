<?php

namespace Tests\Feature;

use App\Post;
use App\Commente;
use App\Block;
use App\photo as Photo;
use App\React;
use App\SiteSetting;
use App\SavedPost;
use App\User;
use App\Friend;
use App\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SocialFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Storage::fake('local');
    }

    private function user(string $email='user@example.com'): User
    {
        return User::create(['first_name'=>'Test','last_name'=>'User','email'=>$email,'password'=>bcrypt('Secret123!')]);
    }

    public function test_private_pages_redirect_guests_to_login(): void
    {
        foreach(['/community','/friends','/saved','/memories','/messanger/1'] as $url) $this->get($url)->assertRedirect('/login');
    }

    public function test_search_uses_names_only_and_hides_blocked_and_inactive_accounts(): void
    {
        $viewer = $this->user('viewer-search@example.com');
        $blocked = $this->user('blocked-search@example.com');
        $inactive = $this->user('inactive-search@example.com');
        $inactive->update(['is_active' => false]);
        Block::create(['user_id' => $viewer->id, 'blocked_id' => $blocked->id]);

        $this->actingAs($viewer)->get('/search?q=Test')
            ->assertOk()
            ->assertDontSee('blocked-search@example.com')
            ->assertDontSee('inactive-search@example.com')
            ->assertDontSee('viewer-search@example.com')
            ->assertSee('Test User');

        $this->get('/search?q=inactive-search@example.com')
            ->assertOk()
            ->assertDontSee('/profile/'.$inactive->id);
    }

    public function test_watch_and_video_pages_follow_the_source_post_privacy(): void
    {
        $author = $this->user('video-author@example.com');
        $friend = $this->user('video-friend@example.com');
        $stranger = $this->user('video-stranger@example.com');
        $video = Video::create([
            'user_id' => $author->id,
            'path' => 'video/users/'.$author->id.'/',
            'type' => '.mp4',
            'title' => 'Friends-only video',
        ]);
        Post::create([
            'user_id' => $author->id,
            'post_text' => 'Friends only',
            'video' => json_encode([[$video->id]], JSON_FORCE_OBJECT),
            'visibility' => 'friends',
            'status' => true,
        ]);
        Friend::create(['user_id' => $author->id, 'friends_id' => $friend->id, 'state' => 1]);

        $this->actingAs($stranger)->get('/watch')->assertOk()
            ->assertViewHas('items', fn ($items) => $items->total() === 0);
        $this->get('/video/'.$video->id)->assertNotFound();
        $this->postJson('/video/like', ['video_id' => $video->id])->assertNotFound();
        $this->postJson('/commentvideo', ['video_id' => $video->id, 'comment' => 'Hidden video'])->assertNotFound();

        $this->actingAs($friend)->get('/watch')->assertOk()
            ->assertViewHas('items', fn ($items) => $items->total() === 1);
        $this->get('/video/'.$video->id)->assertOk();
    }

    public function test_private_video_segments_are_only_served_to_viewers_of_the_source_post(): void
    {
        $owner = $this->user('hls-owner@example.com');
        $friend = $this->user('hls-friend@example.com');
        $stranger = $this->user('hls-stranger@example.com');
        Friend::forceCreate(['user_id' => $owner->id, 'friends_id' => $friend->id, 'state' => true]);
        $video = Video::create([
            'user_id' => $owner->id,
            'path' => 'private/media/videos/users/'.$owner->id.'/',
            'type' => '.mp4',
        ]);
        $playlistPath = $video->path.$video->id.'/playlist.m3u8';
        Storage::disk('local')->put($playlistPath, "#EXTM3U\n");
        Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Friends-only stream',
            'video' => json_encode([[$video->id]]),
            'visibility' => 'friends',
            'status' => true,
        ]);

        $url = route('media.videos.hls', ['video' => $video->id, 'file' => 'playlist.m3u8']);
        $this->actingAs($friend)->get($url)->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.apple.mpegurl')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->actingAs($stranger)->get($url)->assertNotFound();
        $this->get($url)->assertNotFound();
        $this->actingAs($friend)->get(route('media.videos.hls', [
            'video' => $video->id, 'file' => 'private.m3u8',
        ]))->assertNotFound();
    }

    public function test_language_switch_only_offers_supported_locales_and_persists_choice(): void
    {
        $this->get('/language/ar')->assertRedirect()->assertSessionHas('locale', 'ar');
        $this->get('/')->assertOk()->assertSessionHas('locale', 'ar');

        $this->get('/language/en')->assertRedirect()->assertSessionHas('locale', 'en');
        $this->get('/')->assertOk()->assertSessionHas('locale', 'en');

        $this->get('/language/fr')->assertNotFound();
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
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>2,'liked'=>false,'user_id'=>$other->id])
            ->assertOk()
            ->assertJson(['status' => 'ok', 'liked' => true, 'count' => 1, 'reaction_counts' => [2 => 1]]);
        $this->assertDatabaseHas('reacts',['post_id'=>$post->id,'user_id'=>$user->id,'type'=>2]);
        $this->getJson('/post/'.$post->id.'/reactions')->assertOk()->assertJsonFragment([
            'name' => 'Test User', 'type' => 'Love', 'type_id' => 2, 'emoji' => '❤️',
        ]);
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>99,'liked'=>false])->assertUnprocessable();
    }

    public function test_post_reaction_accepts_browser_false_string_and_toggles_count(): void
    {
        $user = $this->user('reaction-toggle@example.com');
        $post = Post::create(['user_id' => $user->id, 'post_text' => 'Toggle me', 'status' => 1]);

        $this->actingAs($user)->postJson('/like', [
            'post_id' => $post->id,
            'type_id' => 1,
            'liked' => 'false',
        ])->assertOk()->assertJson([
            'status' => 'ok', 'liked' => true, 'count' => 1, 'reaction_counts' => [1 => 1],
        ]);
        $this->assertDatabaseHas('reacts', ['post_id' => $post->id, 'user_id' => $user->id, 'type' => 1]);

        $this->postJson('/like', [
            'post_id' => $post->id,
            'type_id' => 1,
            'liked' => 'true',
        ])->assertOk()->assertJson([
            'status' => 'ok', 'liked' => false, 'count' => 0, 'reaction_counts' => [],
        ]);
        $this->assertDatabaseMissing('reacts', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_photo_reaction_accepts_browser_boolean_strings_and_returns_updated_count(): void
    {
        $user = $this->user('photo-reaction@example.com');
        $photo = Photo::create(['user_id' => $user->id, 'path' => 'photos/', 'type' => '.jpg', 'album_id' => 0]);

        $this->actingAs($user)->postJson('/photo/like', [
            'photo_id' => $photo->id,
            'type_id' => 2,
            'liked' => 'false',
        ])->assertOk()->assertJson([
            'status' => 'ok', 'media_type' => 'photo', 'media_id' => $photo->id,
            'liked' => true, 'count' => 1, 'reaction_counts' => [2 => 1],
        ]);
        $this->assertDatabaseHas('photo_react', ['photo_id' => $photo->id, 'user_id' => $user->id]);

        $this->postJson('/photo/like', [
            'photo_id' => $photo->id,
            'type_id' => 2,
            'liked' => 'true',
        ])->assertOk()->assertJson(['liked' => false, 'count' => 0, 'reaction_counts' => []]);
        $this->assertDatabaseMissing('photo_react', ['photo_id' => $photo->id, 'user_id' => $user->id]);
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
        $this->postJson('/like', ['post_id' => $private->id])->assertNotFound();
        $this->postJson('/comment', ['post_id' => $private->id, 'comment' => 'Hidden comment'])->assertNotFound();
        $this->postJson('/saved/'.$private->id)->assertNotFound();
        $this->get('/post/'.$private->id.'/reactions')->assertNotFound();
        $hiddenComment = Commente::create(['user_id' => $owner->id, 'post_id' => $private->id, 'text_co' => 'Private comment']);
        $this->postJson('/likecomment', ['comment_id' => $hiddenComment->id])->assertNotFound();
        $this->postJson('/post-reply', [
            'comment_id' => $hiddenComment->id,
            'userreplay_id' => $owner->id,
            'comment' => 'Private reply',
        ])->assertNotFound();

        $privatePhoto = Photo::create([
            'user_id' => $owner->id,
            'path' => 'private/media/images/users/'.$owner->id.'/',
            'type' => '.jpg',
            'album_id' => 0,
        ]);
        Storage::disk('local')->put($privatePhoto->path.$privatePhoto->id.'.jpg', 'image-bytes');
        $friends->update(['image' => json_encode([[$privatePhoto->id]], JSON_FORCE_OBJECT)]);
        $this->get('/photo/'.$privatePhoto->id)->assertNotFound();
        $this->postJson('/photo/like', ['photo_id' => $privatePhoto->id])->assertNotFound();
        $this->postJson('/commentphoto', ['photo_id' => $privatePhoto->id, 'comment' => 'Hidden photo'])->assertNotFound();

        $this->actingAs($friend)->get('/photo/'.$privatePhoto->id)->assertOk();
        $this->get($privatePhoto->url)->assertOk()->assertHeader('Cache-Control', 'no-store, private');
        $this->actingAs($other)->get($privatePhoto->url)->assertNotFound();
        $this->actingAs($owner)->get($privatePhoto->url)->assertOk();
    }

    public function test_public_reshare_never_bypasses_the_original_posts_privacy(): void
    {
        $owner = $this->user('private-original@example.com');
        $sharer = $this->user('public-sharer@example.com');
        $friend = $this->user('original-friend@example.com');
        $stranger = $this->user('original-stranger@example.com');
        Friend::forceCreate(['user_id' => $owner->id, 'friends_id' => $sharer->id, 'state' => true]);
        Friend::forceCreate(['user_id' => $owner->id, 'friends_id' => $friend->id, 'state' => true]);

        $original = Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Friends-only source',
            'status' => 1,
            'visibility' => 'friends',
        ]);
        $reshare = Post::create([
            'user_id' => $sharer->id,
            'post_text' => 'Public reshare',
            'status' => 1,
            'visibility' => 'public',
            'shared_post_id' => $original->id,
        ]);

        $this->assertFalse(Post::visibleTo(null)->whereKey($reshare->id)->exists());
        $this->assertFalse(Post::visibleTo($stranger)->whereKey($reshare->id)->exists());
        $this->assertTrue(Post::visibleTo($friend)->whereKey($reshare->id)->exists());
        $this->get('/post/'.$reshare->id)->assertNotFound();
        $this->actingAs($friend)->get('/post/'.$reshare->id)->assertOk();
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
            $comment = Commente::findOrFail($commentId);
            Storage::disk('local')->assertExists($comment->media_path);
            $this->get($comment->media_url)->assertOk();
            $this->assertStringNotContainsString('media_path', json_encode($comment->fresh()->toArray()));

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
            Storage::disk('local')->deleteDirectory('private/comment-media/'.$user->id);
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

        $this->actingAs($demo)->get('/')->assertOk()
            ->assertSee('/live/create', false)
            ->assertSee('data-bs-target="#modal-dialog2"', false)
            ->assertSee('data-bs-target="#createStoryModal"', false);
        $this->actingAs($demo)->get('/profile/'.$demo->id)->assertOk()
            ->assertDontSee('href="profile/', false);
        $this->actingAs($demo)->get('/post/1')->assertOk()
            ->assertDontSee('href="profile/', false)
            ->assertSee('btn-focus-comment', false)
            ->assertSee('btn-share-post', false)
            ->assertSee('class="dropdown-item btn-edit-post"', false)
            ->assertSee('class="dropdown-item text-danger btn-delete-post"', false);

        $this->actingAs($demo)->get('/messanger/2')->assertOk()
            ->assertSee('id="contactSearch"', false)
            ->assertSee('aria-label="الأصدقاء"', false)
            ->assertSee('aria-label="ابدأ محادثة جديدة"', false)
            ->assertDontSee('assets/img/user/user-13.jpg');
    }

    public function test_home_sidebar_shows_only_real_unblocked_friends(): void
    {
        $viewer = $this->user('sidebar-viewer@example.com');
        $friend = $this->user('sidebar-friend@example.com');
        $blockedFriend = $this->user('sidebar-blocked@example.com');
        $stranger = $this->user('sidebar-stranger@example.com');
        $friend->update(['first_name' => 'Visible', 'last_name' => 'Friend']);
        $blockedFriend->update(['first_name' => 'Hidden', 'last_name' => 'Blocked']);
        $stranger->update(['first_name' => 'Unrelated', 'last_name' => 'Person']);

        Friend::create(['user_id' => $viewer->id, 'friends_id' => $friend->id, 'state' => true]);
        Friend::create(['user_id' => $blockedFriend->id, 'friends_id' => $viewer->id, 'state' => true]);
        Block::create(['user_id' => $viewer->id, 'blocked_id' => $blockedFriend->id]);

        $this->actingAs($viewer)->get('/')
            ->assertOk()
            ->assertSee('Visible Friend')
            ->assertDontSee('Hidden Blocked')
            ->assertDontSee('Unrelated Person')
            ->assertDontSee('Learn Programming')
            ->assertDontSee('www.learn.com');
    }

    public function test_photo_detail_links_use_root_relative_routes(): void
    {
        $owner = $this->user('photo-links@example.com');
        $photo = Photo::create([
            'user_id' => $owner->id,
            'path' => 'private/media/images/users/'.$owner->id.'/',
            'type' => '.jpg',
            'album_id' => 0,
        ]);
        Storage::disk('local')->put($photo->path.$photo->id.'.jpg', 'image-bytes');

        $this->actingAs($owner)->get('/photo/'.$photo->id)
            ->assertOk()
            ->assertDontSee('href="profile/', false)
            ->assertDontSee('href="photo/', false)
            ->assertSee(url('/profile/'.$owner->id), false)
            ->assertSee(url('/photo/'.$photo->id), false)
            ->assertSee('btn-focus-photo-comment', false)
            ->assertSee('btn-share-photo', false);
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

    public function test_profile_gallery_hides_media_from_posts_the_viewer_cannot_see(): void
    {
        $owner = $this->user('private-gallery-owner@example.com');
        $stranger = $this->user('private-gallery-stranger@example.com');
        $photo = Photo::create([
            'user_id' => $owner->id,
            'path' => 'private/media/images/users/'.$owner->id.'/',
            'type' => '.jpg',
            'album_id' => 0,
        ]);
        Storage::disk('local')->put($photo->path.$photo->id.'.jpg', 'private-image-bytes');
        $video = Video::create([
            'user_id' => $owner->id,
            'path' => 'private/media/videos/users/'.$owner->id.'/',
            'type' => '.mp4',
            'title' => 'Private gallery video',
        ]);
        Post::create([
            'user_id' => $owner->id,
            'post_text' => 'Friends-only gallery media',
            'image' => json_encode([[$photo->id]], JSON_FORCE_OBJECT),
            'video' => json_encode([[$video->id]], JSON_FORCE_OBJECT),
            'visibility' => 'friends',
            'status' => true,
        ]);

        $this->actingAs($stranger)
            ->get('/profile/'.$owner->id)
            ->assertOk()
            ->assertDontSee(route('media.photos.show', $photo->id), false)
            ->assertDontSee(route('media.videos.hls', ['video' => $video->id, 'file' => 'playlist.m3u8']), false)
            ->assertDontSee('Private gallery video');
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
            $photo = Photo::where('user_id', 999)->firstOrFail();
            $video = Video::where('user_id', 999)->firstOrFail();
            $this->assertStringStartsWith('private/media/images/users/999/', $photo->path);
            $this->assertStringStartsWith('private/media/videos/users/999/', $video->path);
            Storage::disk('local')->assertExists($photo->path.$photo->id.'.png');
            Storage::disk('local')->assertExists($video->path.$video->id.'.mp4');
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

    public function test_configured_upload_limit_is_applied_to_posts_profile_gallery_and_stories(): void
    {
        SiteSetting::putValue('max_upload_mb', 1, 'integer');
        $user = $this->user('upload-limit@example.com');
        $largeVideo = fn () => UploadedFile::fake()->create('clip.mp4', 1025, 'video/mp4');

        $this->actingAs($user)->get('/profile/'.$user->id)
            ->assertOk()
            ->assertSee('1 ميجابايت للملف');

        $serviceResult = app(\App\Services\MediaUploadService::class)->processUploads([$largeVideo()]);
        $this->assertSame('error', $serviceResult['status']);
        $this->assertStringContainsString('1 MB', $serviceResult['details']);

        $this->actingAs($user)->postJson('/posts', [
            'post_text' => 'Oversized post media',
            'files' => [$largeVideo()],
        ])->assertUnprocessable()->assertJsonValidationErrors('files.0');

        $this->actingAs($user)->from('/profile/'.$user->id)->post('/photovideo', [
            'files' => [$largeVideo()],
        ])->assertSessionHasErrors('files.0');

        $this->actingAs($user)->postJson('/stories', [
            'type' => 'video',
            'media' => $largeVideo(),
        ])->assertUnprocessable()->assertJsonValidationErrors('media');
    }

    public function test_users_cannot_delete_another_users_post_or_comments(): void
    {
        $author = $this->user('delete-author@example.com');
        $stranger = $this->user('delete-stranger@example.com');
        $post = Post::create(['user_id' => $author->id, 'post_text' => 'Protected post', 'status' => true]);
        $comment = \App\Commente::create([
            'post_id' => $post->id,
            'user_id' => $author->id,
            'text_co' => 'Protected comment',
        ]);
        $photo = Photo::create([
            'user_id' => $author->id,
            'path' => 'private/media/images/users/'.$author->id.'/',
            'type' => '.jpg',
            'album_id' => 0,
        ]);
        $photoComment = \App\Photocommente::create([
            'photo_id' => $photo->id,
            'user_id' => $author->id,
            'comment' => 'Protected photo comment',
        ]);

        $this->actingAs($stranger)->postJson('/postdelete', ['id' => $post->id])->assertForbidden();
        $this->postJson('/commentdelete', ['comment_id' => $comment->id])->assertForbidden();
        $this->postJson('/photocommentdelete', ['comment_id' => $photoComment->id])->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
        $this->assertDatabaseHas('commentes', ['id' => $comment->id]);
        $this->assertDatabaseHas('photocommentes', ['id' => $photoComment->id]);
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
