<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('marketplace_listings') || Schema::hasColumn('marketplace_listings', 'user_id')) {
            return;
        }

        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('category_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('marketplace_listings') || ! Schema::hasColumn('marketplace_listings', 'user_id')) {
            return;
        }

        Schema::table('marketplace_listings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
