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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_mitra')->nullable()->unique();
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->boolean('isAdmin')->default(false);
            $table->boolean('isStockis')->default(false);
            $table->boolean('isMitraBasic')->default(false);
            $table->boolean('isMitraKarir')->default(false);
            $table->string('nama')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('no_rek')->nullable();
            $table->string('nama_rekening')->nullable();
            $table->string('bank')->nullable();
            $table->date('tgl_daftar')->nullable();
            $table->foreignId('id_sponsor')->nullable()->constrained('users')->nullOnDelete();
            $table->string('group_sponsor')->nullable();
            $table->decimal('saldo_penghasilan', 16, 2)->default(0);
            $table->decimal('poin_reward')->default(0);
            $table->string('plan_karir_sekarang')->nullable();
            $table->string('next_plan_karir')->nullable();
            $table->decimal('next_poin_karir')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
