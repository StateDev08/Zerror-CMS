<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('slider_slides') || Schema::hasColumn('slider_slides', 'subtitle')) {
            return;
        }

        Schema::table('slider_slides', function (Blueprint $table) {
            $table->string('subtitle')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('slider_slides') || ! Schema::hasColumn('slider_slides', 'subtitle')) {
            return;
        }

        Schema::table('slider_slides', function (Blueprint $table) {
            $table->dropColumn('subtitle');
        });
    }
};
