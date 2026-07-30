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

        if (! Schema::hasColumn('users', 'discord_link_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('discord_link_token', 64)->nullable()->after('discord_id')->index();
            });
        }

        if (! Schema::hasColumn('users', 'discord_link_token_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('discord_link_token_expires_at')->nullable()->after('discord_link_token');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $drop = array_values(array_filter(
            ['discord_link_token', 'discord_link_token_expires_at'],
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
