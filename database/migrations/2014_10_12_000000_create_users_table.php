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
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('nama');
            $table->string('provinsi');
            $table->string('kabupaten');
            $table->string('alamat');
            $table->string('no_telp');
            $table->string('no_rek');
            $table->string('nama_rekening');
            $table->string('bank');
            $table->date('tgl_daftar');
            $table->foreignId('id_sponsor')->nullable()->constrained('users')->nullOnDelete();
            $table->string('group_sponsor');
            $table->decimal('saldo_penghasilan')->default(0);
            $table->decimal('poin_reward')->default(0);
            $table->string('plan_karir_sekarang');
            $table->string('next_plan_karir');
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
