<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->decimal('max_price', 10, 2)->nullable()->after('custom_request');
            $table->date('desired_date')->nullable()->after('max_price');
            $table->string('priority')->default('normal')->after('desired_date');
            $table->unsignedInteger('quantity')->default(1)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn(['max_price', 'desired_date', 'priority', 'quantity']);
        });
    }
};
