<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanPD;
use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Notifications\LaporanPDVerified;

class LaporanPDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $laporanPDs = LaporanPD::with(['perjalananDinas', 'adminKeuanganVerifier'])->select('laporan_pd.*');

            return DataTables::of($laporanPDs)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.laporan_pd.show', $row->id);
                    $editUrl = route('admin.laporan_pd.edit', $row->id);
                    $deleteUrl = route('admin.laporan_pd.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('perjalananDinas.nomor_surat_tugas', function($row) {
                    return $row->perjalananDinas->nomor_surat_tugas ?? '-';
                })
                ->editColumn('tgl_unggah', function($row) {
                    return $row->tgl_unggah ? \Carbon\Carbon::parse($row->tgl_unggah)->format('d-m-Y H:i') : '-';
                })
                ->editColumn('status_verifikasi', function($row) {
                    $statusClass = '';
                    switch($row->status_verifikasi) {
                        case 'Belum Diverifikasi':
                            $statusClass = 'warning';
                            break;
                        case 'Disetujui':
                            $statusClass = 'success';
                            break;
                        case 'Perbaikan':
                            $statusClass = 'danger';
                            break;
                    }
                    return '<span class="badge bg-'.$statusClass.'">'.$row->status_verifikasi.'</span>';
                })
                ->rawColumns(['action', 'status_verifikasi'])
                ->make(true);
        }

        return view('admin.laporan_pd.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get pegawai assignments for authenticated user
        $user = auth()->user();
        
        if (!$user->pegawai) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan.');
        }
        
        $pegawai = $user->pegawai;
        
        // Get perjalanan dinas assignments for this pegawai that don't have reports yet
        $perjalananDinas = $pegawai->perjalananDinas()
            ->whereDoesntHave('laporanPD', function($query) use ($pegawai) {
                $query->where('pegawai_id', $pegawai->id);
            })
            ->get();

        return view('pegawai.laporan_pd.create', compact('perjalananDinas', 'pegawai'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perjalanan_dinas_id' => 'required|exists:perjalanan_dinas,id|unique:laporan_pd,perjalanan_dinas_id,NULL,id,pegawai_id,' . auth()->user()->pegawai->id,
            'file_laporan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'status_verifikasi' => 'required|in:Belum Diverifikasi,Disetujui,Perbaikan',
            'catatan_verifikasi' => 'nullable|string',
            'admin_keuangan_verifier_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Add pegawai_id from authenticated user
            $data['pegawai_id'] = auth()->user()->pegawai->id;
            
            // Handle file upload
            if ($request->hasFile('file_laporan')) {
                $file = $request->file('file_laporan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('laporan_pd', 'public');
                $data['file_laporan'] = $path;
                $data['tgl_unggah'] = now();
            }

            LaporanPD::create($data);
            
            DB::commit();
            return redirect()->route('pegawai.laporan_pd.my')->with('success', 'Data laporan perjalanan dinas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $laporanPD = LaporanPD::with(['perjalananDinas', 'adminKeuanganVerifier'])->findOrFail($id);
        return view('admin.laporan_pd.show', compact('laporanPD'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporanPD = LaporanPD::findOrFail($id);
        $perjalananDinas = PerjalananDinas::all();
        $adminKeuangan = User::role('Admin Keuangan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.laporan_pd.edit', compact('laporanPD', 'perjalananDinas', 'adminKeuangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $laporanPD = LaporanPD::findOrFail($id);
        
        $request->validate([
            'perjalanan_dinas_id' => 'required|exists:perjalanan_dinas,id|unique:laporan_pd,perjalanan_dinas_id,' . $id,
            'file_laporan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'status_verifikasi' => 'required|in:Belum Diverifikasi,Disetujui,Perbaikan',
            'catatan_verifikasi' => 'nullable|string',
            'admin_keuangan_verifier_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_laporan')) {
                // Delete old file if exists
                if ($laporanPD->file_laporan) {
                    $oldPath = public_path('storage/' . $laporanPD->file_laporan);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('file_laporan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('laporan_pd', 'public');
                $data['file_laporan'] = $path;
                $data['tgl_unggah'] = now(); // Update upload date
            } elseif ($request->has('hapus_file') && $request->hapus_file == 1) {
                // Delete existing file if requested
                if ($laporanPD->file_laporan) {
                    $oldPath = public_path('storage/' . $laporanPD->file_laporan);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['file_laporan'] = null;
            } else {
                unset($data['file_laporan']); // Don't update file_laporan if no new file uploaded
                unset($data['tgl_unggah']); // Don't update tgl_unggah if no new file uploaded
            }

            $laporanPD->update($data);
            
            DB::commit();
            return redirect()->route('admin.laporan_pd.index')->with('success', 'Data laporan perjalanan dinas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $laporanPD = LaporanPD::findOrFail($id);
            
            // Delete file if exists
            if ($laporanPD->file_laporan) {
                $path = public_path('storage/' . $laporanPD->file_laporan);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $laporanPD->delete();
            
            return response()->json(['success' => true, 'message' => 'Data laporan perjalanan dinas berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Get verification statistics.
     */
    public function verificationStats(Request $request)
    {
        $today = now()->format('Y-m-d');
        
        $pending = LaporanPD::where('status_verifikasi', 'Belum Diverifikasi')->count();
        $approvedToday = LaporanPD::where('status_verifikasi', 'Disetujui')
            ->whereDate('updated_at', $today)->count();
        $rejectedToday = LaporanPD::where('status_verifikasi', 'Perbaikan')
            ->whereDate('updated_at', $today)->count();
            
        return response()->json([
            'pending' => $pending,
            'approved_today' => $approvedToday,
            'rejected_today' => $rejectedToday
        ]);
    }

    /**
     * Display verification index for Admin Keuangan.
     */
    public function verificationIndex(Request $request)
    {
        if ($request->ajax()) {
            $laporanPDs = LaporanPD::with(['perjalananDinas', 'adminKeuanganVerifier'])
                ->where('status_verifikasi', 'Belum Diverifikasi')
                ->select('laporan_pd.*');

            return DataTables::of($laporanPDs)
                ->addColumn('action', function ($row) {
                    $verifyUrl = route('keuangan.laporan_pd.verify', $row->id);
                    $rejectUrl = route('keuangan.laporan_pd.reject', $row->id);
                    $btn = '<button onclick="verifyLaporan(\'' . $verifyUrl . '\', ' . $row->id . ')" class="btn btn-success btn-sm me-1">Setujui</button>';
                    $btn .= '<button onclick="rejectLaporan(\'' . $rejectUrl . '\', ' . $row->id . ')" class="btn btn-danger btn-sm">Tolak</button>';
                    return $btn;
                })
                ->editColumn('perjalananDinas.nomor_surat_tugas', function($row) {
                    return $row->perjalananDinas->nomor_surat_tugas ?? '-';
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    $pegawai = $row->perjalananDinas->pegawai->first();
                    return $pegawai ? $pegawai->nama_lengkap : '-';
                })
                ->editColumn('tgl_unggah', function($row) {
                    return $row->tgl_unggah ? \Carbon\Carbon::parse($row->tgl_unggah)->format('d-m-Y H:i') : '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('keuangan.laporan_pd.verification');
    }

    /**
     * Verify a laporan PD report.
     */
    public function verify(Request $request, LaporanPD $laporanPD)
    {
        $request->validate([
            'catatan_verifikasi' => 'nullable|string|max:500'
        ]);

        if ($laporanPD->status_verifikasi !== 'Belum Diverifikasi') {
            return response()->json(['message' => 'Only unverified reports can be verified'], 422);
        }

        DB::beginTransaction();
        try {
            $laporanPD->update([
                'status_verifikasi' => 'Disetujui',
                'admin_keuangan_verifier_id' => auth()->id(),
                'catatan_verifikasi' => $request->catatan_verifikasi,
            ]);

            // Send notification to pimpinan
            $pimpinans = User::role('Pimpinan')->get();
            foreach ($pimpinans as $pimpinan) {
                $pimpinan->notify(new LaporanPDVerified($laporanPD, 'verified'));
            }
            
            DB::commit();
            return response()->json(['message' => 'Laporan PD verified successfully and notifications sent to pimpinan']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to verify laporan PD'], 500);
        }
    }

    /**
     * Reject a laporan PD report.
     */
    public function reject(Request $request, LaporanPD $laporanPD)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500'
        ]);

        if ($laporanPD->status_verifikasi !== 'Belum Diverifikasi') {
            return response()->json(['message' => 'Only unverified reports can be rejected'], 422);
        }

        DB::beginTransaction();
        try {
            $laporanPD->update([
                'status_verifikasi' => 'Perbaikan', // Using 'Perbaikan' as status
                'admin_keuangan_verifier_id' => auth()->id(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            // Send notification to pimpinan
            $pimpinans = User::role('Pimpinan')->get();
            foreach ($pimpinans as $pimpinan) {
                $pimpinan->notify(new LaporanPDVerified($laporanPD, 'rejected'));
            }
            
            DB::commit();
            return response()->json(['message' => 'Laporan PD rejected successfully and notifications sent to pimpinan']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to reject laporan PD'], 500);
        }
    }

    /**
     * Display my laporan PD for pegawai.
     */
    public function myReports(Request $request)
    {
        // Get pegawai from authenticated user
        $user = auth()->user();

        // Check if user has pegawai relationship
        if (!$user->pegawai) {
            return redirect()->route('dashboard')
                ->with('error', 'Data pegawai tidak ditemukan. Hubungi administrator.');
        }

        $pegawai = $user->pegawai;

        if ($request->ajax()) {
            $laporanPDs = $pegawai->laporanPD()
                ->with(['perjalananDinas', 'adminKeuanganVerifier'])
                ->select('laporan_pd.*');

            return DataTables::of($laporanPDs)
                ->addColumn('action', function ($row) {
                    $showUrl = route('pegawai.laporan_pd.show', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    return $btn;
                })
                ->addColumn('perjalanan_dinas', function ($row) {
                    return $row->perjalananDinas->nomor_surat_tugas ?? '-';
                })
                ->editColumn('tgl_unggah', function ($row) {
                    return $row->tgl_unggah ? Carbon\Carbon::parse($row->tgl_unggah)->format('d-m-Y H:i') : '-';
                })
                ->editColumn('status_verifikasi', function ($row) {
                    $statusClass = '';
                    switch ($row->status_verifikasi) {
                        case 'Belum Diverifikasi':
                            $statusClass = 'warning';
                            break;
                        case 'Disetujui':
                            $statusClass = 'success';
                            break;
                        case 'Perbaikan':
                            $statusClass = 'danger';
                            break;
                    }
                    return '<span class="badge bg-' . $statusClass . '">' . $row->status_verifikasi . '</span>';
                })
                ->rawColumns(['action', 'status_verifikasi'])
                ->make(true);
        }

        return view('pegawai.laporan_pd.my_reports', compact('pegawai'));
    }
}