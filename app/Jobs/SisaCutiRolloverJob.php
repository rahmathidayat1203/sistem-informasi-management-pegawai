<?php

namespace App\Jobs;

use App\Models\SisaCuti;
use App\Models\Pegawai;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SisaCutiRolloverJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $year;

    /**
     * Create a new job instance.
     */
    public function __construct(int $year = null)
    {
        $this->year = $year ?? (int) now()->year;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting Sisa Cuti Rollover for year {$this->year}");

        DB::beginTransaction();
        try {
            // Get current year and two years ago (to delete)
            $currentYear = $this->year;
            $twoYearsAgo = $currentYear - 2;

            // 1. Delete sisa cuti that are 2+ years old (hangus)
            $deletedCount = SisaCuti::where('tahun', '<=', $twoYearsAgo)->delete();
            Log::info("Deleted {$deletedCount} expired sisa cuti records from year {$twoYearsAgo} and earlier");

            // 2. Create new sisa cuti records for current year for all active pegawai
            $pegawai = Pegawai::all();
            $createdCount = 0;

            foreach ($pegawai as $p) {
                // Check if pegawai already has sisa cuti for current year
                $existingCuti = SisaCuti::where('pegawai_id', $p->id)
                    ->where('tahun', $currentYear)
                    ->first();

                if (!$existingCuti) {
                    SisaCuti::create([
                        'pegawai_id' => $p->id,
                        'tahun' => $currentYear,
                        'jatah_cuti' => 12, // Standard cuti tahunan
                        'sisa_cuti' => 12,
                    ]);
                    $createdCount++;
                }
            }

            Log::info("Created {$createdCount} new sisa cuti records for year {$currentYear}");

            // 3. Log summary
            $summary = SisaCuti::select('tahun', DB::raw('count(*) as pegawai_count'), DB::raw('sum(sisa_cuti) as total_sisa_cuti'))
                ->whereIn('tahun', [$currentYear - 1, $currentYear])
                ->groupBy('tahun')
                ->get();

            Log::info("Sisa Cuti Summary after rollover:", $summary->toArray());

            DB::commit();
            Log::info("Sisa Cuti Rollover completed successfully");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Sisa Cuti Rollover failed: " . $e->getMessage());
            throw $e;
        }
    }
}
