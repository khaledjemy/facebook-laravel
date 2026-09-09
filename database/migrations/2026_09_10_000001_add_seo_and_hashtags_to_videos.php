<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('seo_title', 160)->nullable()->after('description');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('keywords')->nullable()->after('seo_description');
            $table->string('thumbnail_path')->nullable()->after('keywords');
        });

        Schema::create('hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->unsignedBigInteger('posts_count')->default(0);
            $table->timestamps();
        });

        Schema::create('hashtag_post', function (Blueprint $table) {
            $table->unsignedBigInteger('hashtag_id');
            $table->unsignedBigInteger('post_id');
            $table->primary(['hashtag_id', 'post_id']);
            $table->foreign('hashtag_id')->references('id')->on('hashtags')->cascadeOnDelete();
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hashtag_post');
        Schema::dropIfExists('hashtags');
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'keywords', 'thumbnail_path']);
        });
    }
};
