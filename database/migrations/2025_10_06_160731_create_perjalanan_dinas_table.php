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
        Schema::create('perjalanan_dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat_tugas');
            $table->text('maksud_perjalanan');
            $table->string('tempat_tujuan');
            $table->date('tgl_berangkat');
            $table->date('tgl_kembali');
            $table->unsignedBigInteger('pimpinan_pemberi_tugas_id'); // Foreign key to users table
            $table->timestamps();

            $table->foreign('pimpinan_pemberi_tugas_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanan_dinas');
    }
};
