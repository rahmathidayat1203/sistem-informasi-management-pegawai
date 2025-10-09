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
        if (!Schema::hasTable('cuti')) {
            return;
        }

        if (!Schema::hasColumn('cuti', 'alokasi_sisa_cuti')) {
            Schema::table('cuti', function (Blueprint $table) {
                $table->json('alokasi_sisa_cuti')->nullable()->after('dokumen_pendukung');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('cuti')) {
            return;
        }

        if (Schema::hasColumn('cuti', 'alokasi_sisa_cuti')) {
            Schema::table('cuti', function (Blueprint $table) {
                $table->dropColumn('alokasi_sisa_cuti');
            });
        }
    }
};
