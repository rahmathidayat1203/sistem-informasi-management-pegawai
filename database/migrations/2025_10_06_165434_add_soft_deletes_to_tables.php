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
        // Add soft deletes to tables that don't have it but need it
        Schema::table('cuti', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('pendidikan', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('keluarga', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('laporan_pd', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('pendidikan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('laporan_pd', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};