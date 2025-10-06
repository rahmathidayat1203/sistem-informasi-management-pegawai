<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanPD;
use App\Models\PerjalananDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

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
        $perjalananDinas = PerjalananDinas::all();
        $adminKeuangan = User::role('Admin Keuangan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.laporan_pd.create', compact('perjalananDinas', 'adminKeuangan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'perjalanan_dinas_id' => 'required|exists:perjalanan_dinas,id|unique:laporan_pd,perjalanan_dinas_id',
            'file_laporan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'status_verifikasi' => 'required|in:Belum Diverifikasi,Disetujui,Perbaikan',
            'catatan_verifikasi' => 'nullable|string',
            'admin_keuangan_verifier_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
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
            return redirect()->route('admin.laporan_pd.index')->with('success', 'Data laporan perjalanan dinas berhasil ditambahkan.');
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
}