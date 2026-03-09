<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clan_leaderboard_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('season')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('clan_leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clan_leaderboard_category_id')->constrained()->cascadeOnDelete();
            $table->string('player_name');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clan_leaderboard_entries');
        Schema::dropIfExists('clan_leaderboard_categories');
    }
};
