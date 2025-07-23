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
        Schema::create('deviden_harians', function (Blueprint $table) {
            $table->id();
            $table->decimal('omzet_aktivasi', 15, 2)->nullable();
            $table->decimal('omzet_ro_basic', 15, 2)->nullable();
            $table->integer('total_member')->nullable();
            $table->decimal('deviden_diterima', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deviden_harians');
    }
};
