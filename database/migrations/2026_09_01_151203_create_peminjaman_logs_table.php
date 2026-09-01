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
        Schema::create('peminjaman_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->cascadeOnDelete();
            $table->string('dari_status', 30)->nullable();
            $table->string('ke_status', 30);
            $table->foreignId('aktor_id')->nullable()->constrained('users');
            $table->string('aktor_peran', 20);
            $table->string('catatan', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->index(['peminjaman_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_logs');
    }
};
