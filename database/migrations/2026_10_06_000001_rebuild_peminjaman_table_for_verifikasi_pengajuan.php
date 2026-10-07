<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rebuild tabel `peminjaman` untuk alur verifikasi pengajuan:
     * - kolom baru `catatan_verifikasi` (pesan revisi pengelola);
     * - status default `menunggu_verifikasi`;
     * - kolom generated `status_aktif` mengunci slot juga pada status `ditolak`.
     *
     * SQLite tidak dapat mengubah stored generated column secara in-place,
     * sehingga tabel dibangun ulang lalu data disalin.
     */
    public function up(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::create('peminjaman_baru', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->unique();
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('fasilitas_id')->constrained('fasilitas');
                $table->foreignId('slot_sesi_id')->constrained('slot_sesi');
                $table->date('tanggal');
                $table->string('status', 30)->default('menunggu_verifikasi');
                $table->string('catatan_verifikasi', 255)->nullable();
                $table->dateTime('expired_at')->nullable();
                $table->timestamps();

                // Safety net DB-level (RULES §6): kolom generated berisi status jika
                // peminjaman sedang mengunci slot, NULL jika tidak. `ditolak` ikut
                // mengunci agar slot tetap aman selama penyewa merevisi.
                $table->string('status_aktif', 30)->nullable()->storedAs(
                    "case when status in ('menunggu_verifikasi','ditolak','disetujui','menunggu_pembayaran') then status else null end"
                );
                $table->unique(['fasilitas_id', 'tanggal', 'slot_sesi_id', 'status_aktif']);
                $table->index(['fasilitas_id', 'tanggal']);
                $table->index(['user_id', 'status']);
            });

            // Salin data tanpa kolom generated (dihitung ulang otomatis).
            DB::statement(
                'INSERT INTO peminjaman_baru (id, kode, user_id, fasilitas_id, slot_sesi_id, tanggal, status, catatan_verifikasi, expired_at, created_at, updated_at)
                 SELECT id, kode, user_id, fasilitas_id, slot_sesi_id, tanggal, status, NULL, expired_at, created_at, updated_at FROM peminjaman'
            );

            Schema::dropIfExists('peminjaman');
            Schema::rename('peminjaman_baru', 'peminjaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::create('peminjaman_lama', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->unique();
                $table->foreignId('user_id')->constrained('users');
                $table->foreignId('fasilitas_id')->constrained('fasilitas');
                $table->foreignId('slot_sesi_id')->constrained('slot_sesi');
                $table->date('tanggal');
                $table->string('status', 30)->default('menunggu_pembayaran');
                $table->dateTime('expired_at')->nullable();
                $table->timestamps();

                $table->string('status_aktif', 30)->nullable()->storedAs(
                    "case when status in ('menunggu_pembayaran','menunggu_verifikasi','disetujui') then status else null end"
                );
                $table->unique(['fasilitas_id', 'tanggal', 'slot_sesi_id', 'status_aktif']);
                $table->index(['fasilitas_id', 'tanggal']);
                $table->index(['user_id', 'status']);
            });

            DB::statement(
                'INSERT INTO peminjaman_lama (id, kode, user_id, fasilitas_id, slot_sesi_id, tanggal, status, expired_at, created_at, updated_at)
                 SELECT id, kode, user_id, fasilitas_id, slot_sesi_id, tanggal, status, expired_at, created_at, updated_at FROM peminjaman'
            );

            Schema::dropIfExists('peminjaman');
            Schema::rename('peminjaman_lama', 'peminjaman');
        });
    }
};
