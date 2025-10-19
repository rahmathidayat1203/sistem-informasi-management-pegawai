<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerjalananDinas;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Notifications\PerjalananDinasAssigned;
use Barryvdh\DomPDF\facade\Pdf;
use Illuminate\Support\Facades\Log;

class PerjalananDinasController extends Controller
{
    // Apply permissions at route level instead of constructor to avoid middleware issues

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai'])->select('perjalanan_dinas.*');

            return DataTables::of($perjalananDinas)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.perjalanan_dinas.show', $row->id);
                    $editUrl = route('admin.perjalanan_dinas.edit', $row->id);
                    $deleteUrl = route('admin.perjalanan_dinas.destroy', $row->id);
                    $exportUrl = route('admin.perjalanan_dinas.export.single.pdf', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<a href="' . $exportUrl . '" class="btn btn-warning btn-sm me-1" title="Export PDF">
                                <i class="fas fa-file-pdf"></i> PDF
                              </a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pimpinanPemberiTugas.name', function ($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->editColumn('pegawai', function ($row) {
                    return $row->pegawai->pluck('nama_lengkap')->join(', ') ?: 'Tidak ada pegawai';
                })
                ->editColumn('tgl_berangkat', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_kembali)->format('d-m-Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.perjalanan_dinas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        $pegawais = Pegawai::all();
        return view('admin.perjalanan_dinas.create', compact('pimpinans', 'pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nomor_surat_tugas' => 'required|string|max:255',
            'maksud_perjalanan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'pimpinan_pemberi_tugas_id' => 'required|exists:users,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        DB::beginTransaction();
        try {
            // Create the main perjalanan dinas record
            $perjalananDinas = PerjalananDinas::create([
                'nomor_surat_tugas' => $request->nomor_surat_tugas,
                'maksud_perjalanan' => $request->maksud_perjalanan,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tgl_berangkat' => $request->tgl_berangkat,
                'tgl_kembali' => $request->tgl_kembali,
                'pimpinan_pemberi_tugas_id' => $request->pimpinan_pemberi_tugas_id,
            ]);

            // Attach pegawai to this perjalanan dinas
            $perjalananDinas->pegawai()->attach($request->pegawai_ids);

            DB::commit();

            // Send notifications AFTER commit
            $this->sendNotifications($perjalananDinas, $request->pegawai_ids);

            return redirect()->route('admin.perjalanan_dinas.index')
                ->with('success', 'Data perjalanan dinas berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating perjalanan dinas: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai', 'laporanPD'])->findOrFail($id);
        return view('admin.perjalanan_dinas.show', compact('perjalananDinas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $perjalananDinas = PerjalananDinas::with('pegawai')->findOrFail($id);
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        $pegawais = Pegawai::all();
        return view('admin.perjalanan_dinas.edit', compact('perjalananDinas', 'pimpinans', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $perjalananDinas = PerjalananDinas::with('pegawai')->findOrFail($id);

        $request->validate([
            'nomor_surat_tugas' => 'required|string|max:255|unique:perjalanan_dinas,nomor_surat_tugas,' . $id,
            'maksud_perjalanan' => 'required|string',
            'tempat_tujuan' => 'required|string|max:255',
            'tgl_berangkat' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_berangkat',
            'pimpinan_pemberi_tugas_id' => 'required|exists:users,id',
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        DB::beginTransaction();
        try {
            // Get current pegawai before update
            $currentPegawaiIds = $perjalananDinas->pegawai->pluck('id')->toArray();

            // Update the main perjalanan dinas record
            $perjalananDinas->update([
                'nomor_surat_tugas' => $request->nomor_surat_tugas,
                'maksud_perjalanan' => $request->maksud_perjalanan,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tgl_berangkat' => $request->tgl_berangkat,
                'tgl_kembali' => $request->tgl_kembali,
                'pimpinan_pemberi_tugas_id' => $request->pimpinan_pemberi_tugas_id,
            ]);

            // Update pegawai assignments
            $perjalananDinas->pegawai()->sync($request->pegawai_ids);

            // Get newly assigned pegawai IDs
            $newPegawaiIds = array_diff($request->pegawai_ids, $currentPegawaiIds);

            DB::commit();

            // Send notifications to newly assigned pegawais AFTER commit
            if (!empty($newPegawaiIds)) {
                $this->sendNotifications($perjalananDinas, $newPegawaiIds);
            }

            return redirect()->route('admin.perjalanan_dinas.index')
                ->with('success', 'Data perjalanan dinas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating perjalanan dinas: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
    /**
     * API endpoint for Select2 pegawai search
     */
    public function searchPegawai(Request $request)
    {
        $search = $request->q;

        // For debugging: return all if search is empty or too short
        if (empty($search) || strlen($search) < 1) {
            $pegawais = Pegawai::select('id', 'nama_lengkap', 'NIP')
                ->whereNotNull('nama_lengkap')
                ->whereNotNull('NIP')
                ->limit(10)
                ->get();
            return response()->json($pegawais);
        }

        $pegawais = Pegawai::select('id', 'nama_lengkap', 'NIP')
            ->where(function ($query) use ($search) {
                $query->where('nama_lengkap', 'LIKE', '%' . $search . '%')
                    ->orWhere('NIP', 'LIKE', '%' . $search . '%');
            })
            ->whereNotNull('nama_lengkap')
            ->whereNotNull('NIP')
            ->limit(50)
            ->get();

        return response()->json($pegawais);
    }

    private function sendNotifications(PerjalananDinas $perjalananDinas, array $pegawaiIds)
    {
        // Refresh model to get latest data with relationships
        $perjalananDinas = $perjalananDinas->fresh(['pegawai', 'pimpinanPemberiTugas']);

        // Get users that are linked to these pegawais
        $users = User::whereIn('pegawai_id', $pegawaiIds)->get();

        foreach ($users as $user) {
            try {
                // Send notification
                $user->notify(new PerjalananDinasAssigned($perjalananDinas));

                Log::info('Notification sent to user: ' . $user->id . ' (Pegawai: ' . $user->pegawai_id . ')');
            } catch (\Exception $e) {
                // Log error but continue with other notifications
                Log::error('Failed to send notification to user ' . $user->id . ': ' . $e->getMessage());
            }
        }

        // Log if some pegawais don't have users
        $usersCount = $users->count();
        $pegawaiCount = count($pegawaiIds);
        if ($usersCount < $pegawaiCount) {
            Log::warning("Only {$usersCount} out of {$pegawaiCount} pegawais have user accounts");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $perjalananDinas = PerjalananDinas::findOrFail($id);

            // Remove pegawai-perjalanan_dinas associations first
            $perjalananDinas->pegawai()->detach();

            $perjalananDinas->delete();

            return response()->json(['success' => true, 'message' => 'Data perjalanan dinas berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Display perjalanan dinas assignments for pimpinan.
     */
    public function assignmentIndex(Request $request)
    {
        if ($request->ajax()) {
            $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai'])->get();

            return DataTables::of($perjalananDinas)
                ->addColumn('action', function ($row) {
                    $assignUrl = route('pimpinan.perjalanan_dinas.assign', $row->id);
                    $btn = '<button onclick="assignData(\'' . $assignUrl . '\', ' . $row->id . ')" class="btn btn-warning btn-sm">Assign Pegawai</button>';
                    return $btn;
                })
                ->editColumn('pimpinanPemberiTugas.name', function ($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->editColumn('pegawai', function ($row) {
                    return $row->pegawai->pluck('nama_lengkap')->join(', ') ?: 'Belum ada pegawai';
                })
                ->editColumn('tgl_berangkat', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_kembali)->format('d-m-Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.perjalanan_dinas.assignment');
    }

    /**
     * Assign pegawai to perjalanan dinas.
     */
    public function assign(Request $request, PerjalananDinas $perjalananDinas)
    {
        $request->validate([
            'pegawai_ids' => 'required|array|min:1',
            'pegawai_ids.*' => 'exists:pegawai,id',
        ]);

        DB::beginTransaction();
        try {
            // Get current assignments
            $currentPegawaiIds = $perjalananDinas->pegawai->pluck('id')->toArray();

            // Remove existing assignments and add new ones
            $perjalananDinas->pegawai()->sync($request->pegawai_ids);

            // Get newly assigned pegawai IDs
            $newPegawaiIds = array_diff($request->pegawai_ids, $currentPegawaiIds);

            DB::commit();

            // Send notifications to all assigned pegawais AFTER commit
            // For assignment, we notify all pegawais
            $this->sendNotifications($perjalananDinas, $request->pegawai_ids);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pegawai berhasil ditugaskan.'
                ]);
            }

            return redirect()->back()->with('success', 'Pegawai berhasil ditugaskan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error assigning pegawai: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Display my perjalanan dinas assignments for pegawai.
     */
    public function myAssignments(Request $request)
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
            $perjalananDinas = $pegawai->perjalananDinas()
                ->with(['pimpinanPemberiTugas', 'laporanPD'])
                ->select('perjalanan_dinas.*');

            return DataTables::of($perjalananDinas)
                ->addColumn('action', function ($row) use ($pegawai) {
                    $showUrl = route('admin.perjalanan_dinas.show', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';

                    // Check if there's already a laporan PD for this assignment
                    $hasReport = $row->laporanPD()
                        ->where('pegawai_id', $pegawai->id)
                        ->exists();

                    if ($hasReport) {
                        $laporan = $row->laporanPD()
                            ->where('pegawai_id', $pegawai->id)
                            ->first();
                        $btn .= '<a href="' . route('pegawai.laporan_pd.show', $laporan->id) . '" class="btn btn-success btn-sm">Lihat Laporan</a>';
                    } else {
                        $createLaporanUrl = route('pegawai.laporan_pd.create', ['perjalanan_dinas_id' => $row->id]);
                        $btn .= '<a href="' . $createLaporanUrl . '" class="btn btn-primary btn-sm">Buat Laporan</a>';
                    }

                    return $btn;
                })
                ->addColumn('pimpinan', function ($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->addColumn('status', function ($row) use ($pegawai) {
                    $now = \Carbon\Carbon::now();
                    $tglBerangkat = \Carbon\Carbon::parse($row->tgl_berangkat);
                    $tglKembali = \Carbon\Carbon::parse($row->tgl_kembali);

                    if ($now->lt($tglBerangkat)) {
                        return '<span class="badge bg-warning">Belum Dimulai</span>';
                    } elseif ($now->between($tglBerangkat, $tglKembali)) {
                        return '<span class="badge bg-info">Sedang Berlangsung</span>';
                    } else {
                        // Check if report exists
                        $hasReport = $row->laporanPD()
                            ->where('pegawai_id', $pegawai->id)
                            ->exists();

                        if ($hasReport) {
                            return '<span class="badge bg-success">Selesai (Laporan)</span>';
                        } else {
                            return '<span class="badge bg-danger">Belum Laporan</span>';
                        }
                    }
                })
                ->editColumn('tgl_berangkat', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function ($row) {
                    return \Carbon\Carbon::parse($row->tgl_kembali)->format('d-m-Y');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.pegawai.perjalanan_dinas.my_assignments', compact('pegawai'));
    }

    /**
     * Export perjalanan dinas data to PDF
     */
    public function exportPdf(Request $request)
    {
        $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai', 'pegawai.unitKerja'])
            ->when($request->pimpinan_pemberi_tugas_id, function ($query) use ($request) {
                $query->where('pimpinan_pemberi_tugas_id', $request->pimpinan_pemberi_tugas_id);
            })
            ->when($request->tanggal_mulai && $request->tanggal_selesai, function ($query) use ($request) {
                $query->whereBetween('tgl_berangkat', [$request->tanggal_mulai, $request->tanggal_selesai]);
            })
            ->orderBy('tgl_berangkat', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.perjalanan_dinas.pdf', compact('perjalananDinas'))
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('data_perjalanan_dinas_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Export single perjalanan dinas data to PDF
     */
    public function exportSinglePdf($id)
    {
        $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai', 'pegawai.unitKerja'])
            ->findOrFail($id);

        $pdf = PDF::loadView('admin.perjalanan_dinas.single_pdf', compact('perjalananDinas'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('perjalanan_dinas_' . $perjalananDinas->nomor_surat_tugas . '_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
