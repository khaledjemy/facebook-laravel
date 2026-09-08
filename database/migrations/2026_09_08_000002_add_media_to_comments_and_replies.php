<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commentes', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('text_co');
            $table->string('media_type', 10)->nullable()->after('media_path');
        });
        Schema::table('replies', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('reply');
            $table->string('media_type', 10)->nullable()->after('media_path');
        });
    }

    public function down(): void
    {
        Schema::table('commentes', fn (Blueprint $table) => $table->dropColumn(['media_path', 'media_type']));
        Schema::table('replies', fn (Blueprint $table) => $table->dropColumn(['media_path', 'media_type']));
    }
};
