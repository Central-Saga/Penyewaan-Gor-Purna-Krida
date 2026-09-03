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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->unsignedInteger('nominal');
            $table->string('metode', 20);
            $table->string('status', 30)->default('menunggu_verifikasi');
            $table->string('catatan_verifikasi', 255)->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users');
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
