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
        Schema::create('laporan_pd', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perjalanan_dinas_id');
            $table->string('file_laporan');
            $table->dateTime('tgl_unggah');
            $table->enum('status_verifikasi', ['Belum Diverifikasi', 'Disetujui', 'Perbaikan'])->default('Belum Diverifikasi');
            $table->text('catatan_verifikasi')->nullable();
            $table->unsignedBigInteger('admin_keuangan_verifier_id')->nullable(); // Foreign key to users table
            $table->timestamps();

            $table->foreign('perjalanan_dinas_id')->references('id')->on('perjalanan_dinas')->onDelete('cascade');
            $table->foreign('admin_keuangan_verifier_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pd');
    }
};
