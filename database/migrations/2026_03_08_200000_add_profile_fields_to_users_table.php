<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $columns = [
            'avatar' => fn (Blueprint $table) => $table->string('avatar')->nullable()->after('remember_token'),
            'biography' => fn (Blueprint $table) => $table->text('biography')->nullable()->after('avatar'),
            'job' => fn (Blueprint $table) => $table->string('job', 191)->nullable()->after('biography'),
            'about_me' => fn (Blueprint $table) => $table->text('about_me')->nullable()->after('job'),
            'location' => fn (Blueprint $table) => $table->string('location', 191)->nullable()->after('about_me'),
            'website' => fn (Blueprint $table) => $table->string('website', 255)->nullable()->after('location'),
            'discord_handle' => fn (Blueprint $table) => $table->string('discord_handle', 191)->nullable()->after('website'),
        ];

        foreach ($columns as $name => $definition) {
            if (Schema::hasColumn('users', $name)) {
                continue;
            }
            Schema::table('users', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $drop = array_values(array_filter(
            ['avatar', 'biography', 'job', 'about_me', 'location', 'website', 'discord_handle'],
            fn (string $column) => Schema::hasColumn('users', $column)
        ));

        if ($drop === []) {
            return;
        }

        Schema::table('users', function (Blueprint $table) use ($drop) {
            $table->dropColumn($drop);
        });
    }
};
