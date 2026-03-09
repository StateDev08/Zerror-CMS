<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discord_quick_commands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Slash-Command-Name (z. B. spitzhacke)
            $table->string('description')->default(''); // Kurzbeschreibung für Discord
            $table->text('response_text'); // Was der Bot antwortet (oder Template)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_public')->default(true); // Für alle nutzbar
            $table->integer('use_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discord_quick_commands');
    }
};
