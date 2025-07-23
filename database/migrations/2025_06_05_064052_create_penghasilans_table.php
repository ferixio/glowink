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
        Schema::create('penghasilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('kategori_bonus', [
                'Bonus Sponsor',
                'Bonus Generasi',
                'Bonus Reward',
                'deviden harian',
                'deviden bulanan',
            ])->nullable();

            $table->string('status_qr');
            $table->date('tgl_dapat_bonus');
            $table->string('keterangan');
            $table->decimal('nominal_bonus', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghasilans');
    }
};
