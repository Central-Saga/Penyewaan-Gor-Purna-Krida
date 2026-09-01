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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('fasilitas_id')->constrained('fasilitas');
            $table->foreignId('slot_sesi_id')->constrained('slot_sesi');
            $table->date('tanggal');
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();

            // Safety net DB-level (RULES §6): kolom generated berisi status jika
            // peminjaman sedang mengunci slot, NULL jika tidak. MySQL mengizinkan
            // banyak NULL dalam unique key, jadi slot lepas otomatis saat
            // dibatalkan/selesai.
            $table->string('status_aktif', 30)->nullable()->storedAs(
                "case when status in ('menunggu_pembayaran','menunggu_verifikasi','disetujui') then status else null end"
            );
            $table->unique(['fasilitas_id', 'tanggal', 'slot_sesi_id', 'status_aktif']);
            $table->index(['fasilitas_id', 'tanggal']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
