<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\PerjalananDinas;
use App\Models\LaporanPD;
use App\Models\Pegawai;
use App\Models\SisaCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->hasRole('Admin Kepegawaian')) {
            return $this->adminDashboard($request);
        } elseif ($user->hasRole('Pimpinan')) {
            return $this->pimpinanDashboard($request);
        } elseif ($user->hasRole('Admin Keuangan')) {
            return $this->keuanganDashboard($request);
        } elseif ($user->hasRole('Pegawai')) {
            return $this->pegawaiDashboard($request);
        }

        return view('dashboard.default');
    }

    /**
     * Admin Kepegawaian Dashboard
     */
    private function adminDashboard(Request $request)
    {
        $stats = [
            'total_pegawai' => Pegawai::count(),
            'pegawai_aktif' => Pegawai::whereDoesntHave('user')->count() + Pegawai::whereHas('user')->count(),
            'total_cuti' => Cuti::count(),
            'cuti_pending' => Cuti::where('status_persetujuan', 'Diajukan')->count(),
            'total_perjalanan_dinas' => PerjalananDinas::count(),
            'perjalanan_dinas_aktif' => PerjalananDinas::where('tgl_kembali', '>=', now())->count(),
        ];

        // Recent activities
        $recentCuti = Cuti::with('pegawai')->latest()->take(5)->get();
        $recentPerjalanan = PerjalananDinas::with('pegawai')->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentCuti', 'recentPerjalanan'));
    }

    /**
     * Pimpinan Dashboard
     */
    private function pimpinanDashboard(Request $request)
    {
        $stats = [
            'cuti_pending' => Cuti::where('status_persetujuan', 'Diajukan')->count(),
            'cuti_disetujui_bulan_ini' => Cuti::where('status_persetujuan', 'Disetujui')
                ->whereMonth('updated_at', now()->month)->count(),
            'perjalanan_dinas_aktif' => PerjalananDinas::where('pimpinan_pemberi_tugas_id', Auth::id())
                ->where('tgl_kembali', '>=', now())->count(),
            'total_pegawai' => Pegawai::count(),
        ];

        // Pending approvals
        $pendingCuti = Cuti::with(['pegawai', 'jenisCuti'])
            ->where('status_persetujuan', 'Diajukan')
            ->latest()
            ->take(10)
            ->get();

        // My assignments
        $myAssignments = PerjalananDinas::with('pegawai')
            ->where('pimpinan_pemberi_tugas_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.pimpinan', compact('stats', 'pendingCuti', 'myAssignments'));
    }

    /**
     * Admin Keuangan Dashboard
     */
    private function keuanganDashboard(Request $request)
    {
        $stats = [
            'laporan_pending' => LaporanPD::where('status_verifikasi', 'Diajukan')->count(),
            'laporan verified_bulan_ini' => LaporanPD::where('status_verifikasi', 'Disetujui')
                ->whereMonth('updated_at', now()->month)->count(),
            'total_perjalanan_dinas' => PerjalananDinas::count(),
            'perjalanan_dinas_selesai' => PerjalananDinas::where('tgl_kembali', '<', now())->count(),
        ];

        // Pending verifications
        $pendingLaporan = LaporanPD::with(['perjalananDinas', 'perjalananDinas.pegawai'])
            ->where('status_verifikasi', 'Diajukan')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.keuangan', compact('stats', 'pendingLaporan'));
    }

    /**
     * Pegawai Dashboard
     */
    private function pegawaiDashboard(Request $request)
    {
        $pegawai = Auth::user()->pegawai;
        
        if (!$pegawai) {
            return view('dashboard.pegawai', ['error' => 'Data pegawai tidak ditemukan']);
        }

        // Calculate sisa cuti
        $currentYear = (int) now()->year;
        $sisaCutiRecords = SisaCuti::where('pegawai_id', $pegawai->id)
            ->whereIn('tahun', [$currentYear - 2, $currentYear - 1, $currentYear])
            ->get()
            ->keyBy('tahun');

        $totalSisaCuti = 0;
        for ($i = 0; $i <= 2; $i++) {
            $year = $currentYear - $i;
            $record = $sisaCutiRecords->get($year);
            if ($record) {
                $totalSisaCuti += $record->sisa_cuti;
            }
        }

        $stats = [
            'total_sisa_cuti' => $totalSisaCuti,
            'sisa_cuti_tahun_berjalan' => $sisaCutiRecords->get($currentYear)->sisa_cuti ?? 0,
            'cuti_pending' => Cuti::where('pegawai_id', $pegawai->id)
                ->where('status_persetujuan', 'Diajukan')->count(),
            'cuti_disetujui_tahun_ini' => Cuti::where('pegawai_id', $pegawai->id)
                ->where('status_persetujuan', 'Disetujui')
                ->whereYear('updated_at', $currentYear)->count(),
            'perjalanan_dinas_aktif' => PerjalananDinas::whereHas('pegawai', function($query) use ($pegawai) {
                $query->where('pegawais.id', $pegawai->id);
            })->where('tgl_kembali', '>=', now())->count(),
        ];

        // My recent activities
        $myCuti = Cuti::with('jenisCuti')
            ->where('pegawai_id', $pegawai->id)
            ->latest()
            ->take(5)
            ->get();

        $myPerjalanan = PerjalananDinas::whereHas('pegawai', function($query) use ($pegawai) {
            $query->where('pegawais.id', $pegawai->id);
        })->latest()->take(5)->get();

        return view('dashboard.pegawai', compact('pegawai', 'stats', 'myCuti', 'myPerjalanan'));
    }
}
