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
        Schema::create('detail_deviden_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deviden_bulanan_id')->constrained('deviden_bulanans');
            $table->string('nama_level')->nullable();
            $table->integer('jumlah_mitra')->default(0);
            $table->integer('jumlah_mitra_transaksi')->default(0);
            $table->decimal('omzet_ro_qr', 10, 2)->default(0);
            $table->integer('angka_deviden')->default(0);
            $table->decimal('nominal_deviden_bulanan', 16, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_deviden_bulanans');
    }
};
