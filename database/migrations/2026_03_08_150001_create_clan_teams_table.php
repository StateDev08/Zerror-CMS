<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clan_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('contact')->nullable();
            $table->boolean('visible')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('clan_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clan_team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('clan_member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('display_name');
            $table->string('role')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clan_team_members');
        Schema::dropIfExists('clan_teams');
    }
};
