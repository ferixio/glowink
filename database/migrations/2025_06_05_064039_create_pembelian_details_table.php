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
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->string('nama_produk');
            $table->string('paket');
            $table->integer('jml_beli');
            $table->decimal('harga_beli', 16, 2);
            $table->decimal('cashback', 16, 2)->default(10000);
            $table->decimal('nominal_bonus_sponsor', 16, 2);
            $table->decimal('nominal_bonus_generasi', 16, 2);
            $table->foreignId('user_id_get_bonus_sponsor')->nullable()->constrained('users')->onDelete('set null');
            $table->string('group_user_id_get_bonus_generasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
    }
};
