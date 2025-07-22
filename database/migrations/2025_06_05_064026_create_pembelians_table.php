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
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_beli');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('beli_dari')->constrained('users')->onDelete('cascade');
            $table->string('tujuan_beli');
            $table->string('nama_penerima');
            $table->string('no_telp');
            $table->text('alamat_tujuan');
            $table->decimal('total_beli', 10, 2);
            $table->decimal('total_bonus', 10, 2);
            $table->string('status_pembelian');
            $table->string('images')->nullable();
            $table->enum('kategori_pembelian', [
                'aktivasi member',
                'stock pribadi',
                'repeat order',
            ])->default('stock pribadi');

            $table->integer('jumlah_poin_qr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
