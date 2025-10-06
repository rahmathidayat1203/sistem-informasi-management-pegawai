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
        Schema::table('cuti', function (Blueprint $table) {
            if (!Schema::hasColumn('cuti', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('pendidikan', function (Blueprint $table) {
            if (!Schema::hasColumn('pendidikan', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('keluarga', function (Blueprint $table) {
            if (!Schema::hasColumn('keluarga', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_pangkat', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            if (!Schema::hasColumn('riwayat_jabatan', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('laporan_pd', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_pd', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            if (!Schema::hasColumn('perjalanan_dinas', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuti', function (Blueprint $table) {
            if (Schema::hasColumn('cuti', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('pendidikan', function (Blueprint $table) {
            if (Schema::hasColumn('pendidikan', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('keluarga', function (Blueprint $table) {
            if (Schema::hasColumn('keluarga', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('riwayat_pangkat', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_pangkat', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('riwayat_jabatan', function (Blueprint $table) {
            if (Schema::hasColumn('riwayat_jabatan', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('laporan_pd', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_pd', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
        
        Schema::table('perjalanan_dinas', function (Blueprint $table) {
            if (Schema::hasColumn('perjalanan_dinas', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};