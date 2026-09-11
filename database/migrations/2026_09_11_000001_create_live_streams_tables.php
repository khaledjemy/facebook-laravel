<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('live_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('video_id')->nullable()->constrained('videos')->nullOnDelete();
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->string('visibility', 20)->default('public');
            $table->string('status', 20)->default('preparing');
            $table->unsignedInteger('viewer_count')->default(0);
            $table->unsignedInteger('peak_viewers')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('live_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_stream_id')->constrained('live_streams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('body', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_comments');
        Schema::dropIfExists('live_streams');
    }
};
