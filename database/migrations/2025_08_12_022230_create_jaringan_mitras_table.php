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
        Schema::create('jaringan_mitras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id') // Member yang mendaftar
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('sponsor_id') // Sponsor langsungnya
                ->constrained('users')
                ->onDelete('cascade');
            $table->unsignedInteger('level') // Kedalaman level dari sponsor utama
                ->default(1);
            $table->unique(['user_id', 'sponsor_id', 'level']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jaringan_mitras');
    }
};
