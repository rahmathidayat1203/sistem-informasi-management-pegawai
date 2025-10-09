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
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->unsignedBigInteger('jenis_cuti_id');
            $table->unsignedBigInteger('pimpinan_approver_id')->nullable(); // Foreign key to users table
            $table->date('tgl_pengajuan');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('keterangan');
            $table->enum('status_persetujuan', ['Diajukan', 'Disetujui', 'Ditolak'])->default('Diajukan');
            $table->string('dokumen_pendukung')->nullable();
            $table->json('alokasi_sisa_cuti')->nullable();
            $table->timestamps();

            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
            $table->foreign('jenis_cuti_id')->references('id')->on('jenis_cuti')->onDelete('cascade');
            $table->foreign('pimpinan_approver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
