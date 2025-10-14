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
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pimpinanPemberiTugas.name', function($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->editColumn('pegawai', function($row) {
                    return $row->pegawai->pluck('nama_lengkap')->join(', ') ?: 'Tidak ada pegawai';
                })
                ->editColumn('tgl_berangkat', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function($row) {
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

            // Send notifications to assigned pegawais
            foreach ($perjalananDinas->pegawai as $pegawai) {
                $pegawai->user->notify(new PerjalananDinasAssigned($perjalananDinas));
            }

            DB::commit();
            return redirect()->route('admin.perjalanan_dinas.index')->with('success', 'Data perjalanan dinas berhasil ditambahkan.');
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
            // Update the main perjalanan dinas record
            $perjalananDinas->update([
                'nomor_surat_tugas' => $request->nomor_surat_tugas,
                'maksud_perjalanan' => $request->maksud_perjalanan,
                'tempat_tujuan' => $request->tempat_tujuan,
                'tgl_berangkat' => $request->tgl_berangkat,
                'tgl_kembali' => $request->tgl_kembali,
                'pimpinan_pemberi_tugas_id' => $request->pimpinan_pemberi_tugas_id,
            ]);

            // Sync pegawais for this perjalanan dinas
            $perjalananDinas->pegawai()->sync($request->pegawai_ids);

            DB::commit();
            return redirect()->route('admin.perjalanan_dinas.index')->with('success', 'Data perjalanan dinas berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            ->where(function($query) use ($search) {
                $query->where('nama_lengkap', 'LIKE', '%'.$search.'%')
                      ->orWhere('NIP', 'LIKE', '%'.$search.'%');
            })
            ->whereNotNull('nama_lengkap')
            ->whereNotNull('NIP')
            ->limit(50)
            ->get();
            
        return response()->json($pegawais);
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
                ->editColumn('pimpinanPemberiTugas.name', function($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->editColumn('pegawai', function($row) {
                    return $row->pegawai->pluck('nama_lengkap')->join(', ') ?: 'Belum ada pegawai';
                })
                ->editColumn('tgl_berangkat', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function($row) {
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
            // Remove existing assignments
            $perjalananDinas->pegawai()->detach();

            // Add new assignments
            $perjalananDinas->pegawai()->attach($request->pegawai_ids);

            // Send notifications to assigned pegawais
            foreach ($perjalananDinas->pegawai as $pegawai) {
                if ($pegawai->user) {
                    $pegawai->user->notify(new PerjalananDinasAssigned($perjalananDinas));
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pegawai berhasil ditugaskan.']);
            }

            return redirect()->back()->with('success', 'Pegawai berhasil ditugaskan.');
        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display my perjalanan dinas assignments for pegawai.
     */
    public function myAssignments(Request $request)
    {
        $pegawai = Pegawai::where('user_id', auth()->id())->first();
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan.');
        }

        if ($request->ajax()) {
            $perjalananDinas = $pegawai->perjalananDinas()
                ->with(['pimpinanPemberiTugas'])
                ->select('perjalanan_dinas.*');

            return DataTables::of($perjalananDinas)
                ->addColumn('action', function ($row) use ($pegawai) {
                    $showUrl = route('admin.perjalanan_dinas.show', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    
                    // Check if there's already a laporan PD for this assignment
                    if ($row->laporanPD && $row->laporanPD->pegawai_id == $pegawai->id) {
                        $btn .= '<a href="' . route('pegawai.laporan_pd.show', $row->laporanPD->id) . '" class="btn btn-success btn-sm">Lihat Laporan</a>';
                    } else {
                        $createLaporanUrl = route('pegawai.laporan_pd.create', ['perjalanan_dinas_id' => $row->id]);
                        $btn .= '<a href="' . $createLaporanUrl . '" class="btn btn-primary btn-sm">Buat Laporan</a>';
                    }
                    
                    return $btn;
                })
                ->editColumn('pimpinanPemberiTugas.name', function($row) {
                    return $row->pimpinanPemberiTugas->name ?? '-';
                })
                ->editColumn('status', function($row) {
                    $now = \Carbon\Carbon::now();
                    $tglBerangkat = \Carbon\Carbon::parse($row->tgl_berangkat);
                    $tglKembali = \Carbon\Carbon::parse($row->tgl_kembali);
                    
                    if ($now->lt($tglBerangkat)) {
                        return '<span class="badge bg-warning">Belum Dimulai</span>';
                    } elseif ($now->between($tglBerangkat, $tglKembali)) {
                        return '<span class="badge bg-info">Sedang Berlangsung</span>';
                    } elseif ($row->laporanPD) {
                        return '<span class="badge bg-success">Selesai (Laporan)</span>';
                    } else {
                        return '<span class="badge bg-danger">Belum Laporan</span>';
                    }
                })
                ->editColumn('tgl_berangkat', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_berangkat)->format('d-m-Y');
                })
                ->editColumn('tgl_kembali', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_kembali)->format('d-m-Y');
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('pegawai.perjalanan_dinas.my_assignments', compact('pegawai'));
    }

    /**
     * Export perjalanan dinas data to PDF
     */
    public function exportPdf(Request $request)
    {
        $perjalananDinas = PerjalananDinas::with(['pimpinanPemberiTugas', 'pegawai', 'pegawai.unitKerja'])
            ->when($request->pimpinan_pemberi_tugas_id, function($query) use ($request) {
                $query->where('pimpinan_pemberi_tugas_id', $request->pimpinan_pemberi_tugas_id);
            })
            ->when($request->tanggal_mulai && $request->tanggal_selesai, function($query) use ($request) {
                $query->whereBetween('tgl_berangkat', [$request->tanggal_mulai, $request->tanggal_selesai]);
            })
            ->orderBy('tgl_berangkat', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.perjalanan_dinas.pdf', compact('perjalananDinas'))
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('data_perjalanan_dinas_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}