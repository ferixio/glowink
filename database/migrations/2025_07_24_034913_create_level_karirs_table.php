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
        Schema::create('level_karirs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_level')->nullable();
            $table->integer('minimal_RO_QR')->default(0);
            $table->integer('angka_deviden')->default(0);
            $table->integer('jumlah_mitra_level_ini')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('level_karirs');
    }
};
