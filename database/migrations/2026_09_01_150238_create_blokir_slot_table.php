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
        Schema::create('blokir_slot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->foreignId('slot_sesi_id')->constrained('slot_sesi')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('alasan', 255);
            $table->foreignId('diblokir_oleh')->constrained('users');
            $table->timestamps();
            $table->unique(['fasilitas_id', 'slot_sesi_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blokir_slot');
    }
};
