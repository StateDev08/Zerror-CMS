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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('remember_token');
            $table->text('biography')->nullable()->after('avatar');
            $table->string('job', 191)->nullable()->after('biography');
            $table->text('about_me')->nullable()->after('job');
            $table->string('location', 191)->nullable()->after('about_me');
            $table->string('website', 255)->nullable()->after('location');
            $table->string('discord_handle', 191)->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar', 'biography', 'job', 'about_me',
                'location', 'website', 'discord_handle',
            ]);
        });
    }
};
