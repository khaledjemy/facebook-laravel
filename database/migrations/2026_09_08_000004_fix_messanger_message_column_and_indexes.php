<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messangers', function (Blueprint $table) {
            $table->text('message')->change();
            $table->index(['my_id', 'user_id']);
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->index(['user_id', 'friends_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messangers', function (Blueprint $table) {
            $table->dropIndex(['my_id', 'user_id']);
            $table->string('message', 255)->change();
        });

        Schema::table('friends', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'friends_id']);
        });
    }
};
