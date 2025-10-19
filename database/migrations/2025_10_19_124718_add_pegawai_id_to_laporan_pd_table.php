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
        Schema::table('laporan_pd', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->after('perjalanan_dinas_id');
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_pd', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });
    }
};
