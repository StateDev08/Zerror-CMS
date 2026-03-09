<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clan_treasury_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('clan_treasury_entries', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // income, expense
            $table->decimal('amount', 12, 2);
            $table->foreignId('clan_treasury_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->date('entry_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clan_treasury_entries');
        Schema::dropIfExists('clan_treasury_categories');
    }
};
