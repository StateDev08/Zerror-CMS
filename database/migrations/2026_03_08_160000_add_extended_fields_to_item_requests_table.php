<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('item_requests')) {
            return;
        }

        if (! Schema::hasColumn('item_requests', 'max_price')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->decimal('max_price', 10, 2)->nullable()->after('custom_request');
            });
        }

        if (! Schema::hasColumn('item_requests', 'desired_date')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->date('desired_date')->nullable()->after('max_price');
            });
        }

        if (! Schema::hasColumn('item_requests', 'priority')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->string('priority')->default('normal')->after('desired_date');
            });
        }

        if (! Schema::hasColumn('item_requests', 'quantity')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1)->after('priority');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('item_requests')) {
            return;
        }

        $drop = array_values(array_filter(
            ['max_price', 'desired_date', 'priority', 'quantity'],
            fn (string $column) => Schema::hasColumn('item_requests', $column)
        ));

        if ($drop === []) {
            return;
        }

        Schema::table('item_requests', function (Blueprint $table) use ($drop) {
            $table->dropColumn($drop);
        });
    }
};
