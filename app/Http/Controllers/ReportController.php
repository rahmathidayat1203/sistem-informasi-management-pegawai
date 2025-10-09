<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\PerjalananDinas;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate DUK (Daftar Urut Kepangkatan) report.
     */
    public function duk(Request $request)
    {
        $query = Pegawai::with(['golongan', 'jabatan', 'unitKerja'])
            ->orderByRaw("FIELD(tingkat_pendidikan, 'S3', 'S2', 'S1', 'D4', 'D3', 'SMA/SMK', 'SMP', 'SD')")
            ->orderBy('golongan_id', 'desc')
            ->orderBy('tmt_golongan', 'asc')
            ->orderBy('nama_lengkap', 'asc');

        // Filter by unit kerja
        if ($request->unit_kerja_id) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }

        // Filter by golongan
        if ($request->golongan_id) {
            $query->where('golongan_id', $request->golongan_id);
        }

        $pegawais = $query->get();
        
        return view('reports.duk', compact('pegawais'));
    }

    /**
     * Generate pegawai recap report.
     */
    public function rekapPegawai(Request $request)
    {
        $query = Pegawai::query();

        // Filters
        if ($request->unit_kerja_id) {
            $query->where('unit_kerja_id', $request->unit_kerja_id);
        }

        if ($request->jenis_kelamin) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        if ($request->status_kepegawaian) {
            $query->where('status_kepegawaian', $request->status_kepegawaian);
        }

        $pegawais = $query->with(['golongan', 'jabatan', 'unitKerja'])->get();

        // Statistics
        $stats = [
            'total_pegawai' => $pegawais->count(),
            'per_jenis_kelamin' => $pegawais->groupBy('jenis_kelamin')->map->count(),
            'per_unit_kerja' => $pegawais->groupBy('unitKerja.nama')->map->count(),
            'per_golongan' => $pegawais->groupBy('golongan.nama')->map->count(),
            'per_status' => $pegawais->groupBy('status_kepegawaian')->map->count(),
            'usia_distribution' => $this->calculateAgeDistribution($pegawais),
        ];

        return view('reports.rekap-pegawai', compact('pegawais', 'stats'));
    }

    /**
     * Generate cuti statistics report.
     */
    public function statistikCuti(Request $request)
    {
        $query = Cuti::with(['pegawai', 'jenisCuti']);

        // Filter by year
        if ($request->year) {
            $query->whereYear('created_at', $request->year);
        } else {
            $query->whereYear('created_at', Carbon::now()->year);
        }

        // Filter by unit kerja
        if ($request->unit_kerja_id) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('unit_kerja_id', $request->unit_kerja_id);
            });
        }

        $cutis = $query->get();

        // Statistics
        $stats = [
            'total_pengajuan' => $cutis->count(),
            'per_status' => $cutis->groupBy('status_persetujuan')->map->count(),
            'per_jenis_cuti' => $cutis->groupBy('jenisCuti.nama')->map->count(),
            'per_bulan' => $cutis->groupBy(function($item) {
                return Carbon::parse($item->created_at)->format('F');
            })->map->count(),
            'lama_cuti_rata_rata' => $cutis->avg(function($item) {
                return Carbon::parse($item->tgl_mulai)->diffInDays($item->tgl_selesai) + 1;
            }),
        ];

        return view('reports.statistik-cuti', compact('cutis', 'stats'));
    }

    /**
     * Generate perjalanan dinas report.
     */
    public function laporanPerjalananDinas(Request $request)
    {
        $query = PerjalananDinas::with(['pegawai', 'pimpinanPemberiTugas']);

        // Filter by year
        if ($request->year) {
            $query->whereYear('tgl_berangkat', $request->year);
        } else {
            $query->whereYear('tgl_berangkat', Carbon::now()->year);
        }

        // Filter by unit kerja
        if ($request->unit_kerja_id) {
            $query->whereHas('pegawai', function($q) use ($request) {
                $q->where('unit_kerja_id', $request->unit_kerja_id);
            });
        }

        $perjalananDinas = $query->get();

        // Calculate statistics
        $totalBiaya = $perjalananDinas->sum('biaya');
        $perPegawai = $perjalananDinas->flatMap(function($item) {
            return $item->pegawai->map(function($pegawai) use ($item) {
                return [
                    'nama' => $pegawai->nama_lengkap,
                    'jumlah_perjalanan' => 1,
                    'total_biaya' => $item->biaya / $item->pegawai->count(),
                ];
            });
        })->groupBy('nama');

        $stats = [
            'total_perjalanan' => $perjalananDinas->count(),
            'total_biaya' => $totalBiaya,
            'biaya_rata_rata' => $perjalananDinas->avg('biaya'),
            'per_unit_kerja' => $perjalananDinas->groupBy(function($item) {
                return $item->pegawai->first()->unitKerja->nama ?? 'Tidak Diketahui';
            })->map->count(),
            'pegawai_terbanyak' => $perPegawai->mapWithKeys(function($items, $pegawai) {
                return [$pegawai => $items->sum('jumlah_perjalanan')];
            })->sortDesc()->take(10),
        ];

        return view('reports.laporan-perjalanan-dinas', compact('perjalananDinas', 'stats'));
    }

    /**
     * Export DUK to PDF (placeholder).
     */
    public function exportDukPdf(Request $request)
    {
        // Implementation for PDF export
        return redirect()->back()->with('info', 'Export PDF akan segera tersedia');
    }

    /**
     * Export DUK to Excel (placeholder).
     */
    public function exportDukExcel(Request $request)
    {
        // Implementation for Excel export
        return redirect()->back()->with('info', 'Export Excel akan segera tersedia');
    }

    /**
     * Calculate age distribution for pegawai.
     */
    private function calculateAgeDistribution($pegawais)
    {
        $distribution = [
            '< 25 tahun' => 0,
            '25-34 tahun' => 0,
            '35-44 tahun' => 0,
            '45-54 tahun' => 0,
            '>= 55 tahun' => 0,
        ];

        foreach ($pegawais as $pegawai) {
            $age = Carbon::parse($pegawai->tanggal_lahir)->age;
            
            if ($age < 25) {
                $distribution['< 25 tahun']++;
            } elseif ($age >= 25 && $age <= 34) {
                $distribution['25-34 tahun']++;
            } elseif ($age >= 35 && $age <= 44) {
                $distribution['35-44 tahun']++;
            } elseif ($age >= 45 && $age <= 54) {
                $distribution['45-54 tahun']++;
            } else {
                $distribution['>= 55 tahun']++;
            }
        }

        return $distribution;
    }
}
