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
        Schema::create('sisa_cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade'); // Foreign key ke tabel pegawai
            $table->year('tahun'); // Tahun berlakunya jatah cuti
            $table->integer('jatah_cuti'); // Jumlah total jatah cuti pada tahun tersebut
            $table->integer('sisa_cuti'); // Sisa cuti yang belum terpakai pada tahun tersebut
            $table->timestamps();

            // Menambahkan unique key pada pegawai_id dan tahun agar tidak ada duplikasi
            $table->unique(['pegawai_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sisa_cuti');
    }
};
