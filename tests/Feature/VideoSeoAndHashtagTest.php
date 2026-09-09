<?php

namespace Tests\Feature;

use App\User;
use App\Hashtag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VideoSeoAndHashtagTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_metadata_thumbnail_and_hashtags_are_saved(): void
    {
        Queue::fake();
        $user = User::create(['first_name' => 'Video', 'last_name' => 'Creator', 'email' => 'video@example.com', 'password' => bcrypt('secret')]);
        $thumbnail = 'data:image/jpeg;base64,'.base64_encode('fake-jpeg');

        $response = $this->actingAs($user)->postJson('/posts', [
            'post_text' => 'حلقة جديدة #برمجة',
            'files' => [UploadedFile::fake()->create('lesson.mp4', 100, 'video/mp4')],
            'video_title' => 'تعلم Laravel',
            'video_description' => 'وصف الفيديو #تعليم',
            'video_seo_title' => 'دورة Laravel للمبتدئين',
            'video_seo_description' => 'تعلم بناء التطبيقات خطوة بخطوة',
            'video_keywords' => 'laravel, php, برمجة',
            'hashtags' => '#لارافيل #php',
            'selected_video_thumbnail' => $thumbnail,
        ])->assertOk();

        $postId = $response->json('data.id');
        $this->assertDatabaseHas('videos', ['title' => 'تعلم Laravel', 'seo_title' => 'دورة Laravel للمبتدئين']);
        $this->assertDatabaseHas('hashtags', ['name' => 'برمجة']);
        $this->assertDatabaseHas('hashtags', ['name' => 'لارافيل']);
        $this->assertDatabaseHas('hashtag_post', ['post_id' => $postId]);
    }

    public function test_hashtag_page_lists_visible_posts(): void
    {
        $user = User::create(['first_name' => 'Test', 'last_name' => 'User', 'email' => 'tag@example.com', 'password' => bcrypt('secret')]);
        $this->actingAs($user)->postJson('/posts', ['post_text' => 'محتوى #اختبار'])->assertOk();
        $slug = Hashtag::where('name', 'اختبار')->value('slug');
        $this->get(route('hashtags.show', ['slug' => $slug]))->assertOk()->assertSee('محتوى');
    }
}
