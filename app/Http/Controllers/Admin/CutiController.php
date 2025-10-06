<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\JenisCuti;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cutis = Cuti::with(['pegawai', 'jenisCuti', 'pimpinanApprover'])->select('cuti.*');

            return DataTables::of($cutis)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.cuti.show', $row->id);
                    $editUrl = route('admin.cuti.edit', $row->id);
                    $deleteUrl = route('admin.cuti.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('jenisCuti.nama', function($row) {
                    return $row->jenisCuti->nama ?? '-';
                })
                ->editColumn('tgl_mulai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_mulai)->format('d-m-Y');
                })
                ->editColumn('tgl_selesai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_selesai)->format('d-m-Y');
                })
                ->editColumn('status_persetujuan', function($row) {
                    $statusClass = '';
                    switch($row->status_persetujuan) {
                        case 'Diajukan':
                            $statusClass = 'warning';
                            break;
                        case 'Disetujui':
                            $statusClass = 'success';
                            break;
                        case 'Ditolak':
                            $statusClass = 'danger';
                            break;
                    }
                    return '<span class="badge bg-'.$statusClass.'">'.$row->status_persetujuan.'</span>';
                })
                ->rawColumns(['action', 'status_persetujuan'])
                ->make(true);
        }

        return view('admin.cuti.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        $jenisCutis = JenisCuti::all();
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.cuti.create', compact('pegawais', 'jenisCutis', 'pimpinans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tgl_pengajuan' => 'required|date',
            'tgl_mulai' => 'required|date|after_or_equal:tgl_pengajuan',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string',
            'status_persetujuan' => 'required|in:Diajukan,Disetujui,Ditolak',
            'pimpinan_approver_id' => 'nullable|exists:users,id',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('dokumen_pendukung')) {
                $file = $request->file('dokumen_pendukung');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('dokumen_cuti', 'public');
                $data['dokumen_pendukung'] = $path;
            }

            Cuti::create($data);
            
            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Data cuti berhasil ditambahkan.');
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
        $cuti = Cuti::with(['pegawai', 'jenisCuti', 'pimpinanApprover'])->findOrFail($id);
        return view('admin.cuti.show', compact('cuti'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cuti = Cuti::findOrFail($id);
        $pegawais = Pegawai::all();
        $jenisCutis = JenisCuti::all();
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.cuti.edit', compact('cuti', 'pegawais', 'jenisCutis', 'pimpinans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cuti = Cuti::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tgl_pengajuan' => 'required|date',
            'tgl_mulai' => 'required|date|after_or_equal:tgl_pengajuan',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string',
            'status_persetujuan' => 'required|in:Diajukan,Disetujui,Ditolak',
            'pimpinan_approver_id' => 'nullable|exists:users,id',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('dokumen_pendukung')) {
                // Delete old file if exists
                if ($cuti->dokumen_pendukung) {
                    $oldPath = public_path('storage/' . $cuti->dokumen_pendukung);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('dokumen_pendukung');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('dokumen_cuti', 'public');
                $data['dokumen_pendukung'] = $path;
            } elseif ($request->has('hapus_file') && $request->hapus_file == 1) {
                // Delete existing file if requested
                if ($cuti->dokumen_pendukung) {
                    $oldPath = public_path('storage/' . $cuti->dokumen_pendukung);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['dokumen_pendukung'] = null;
            } else {
                unset($data['dokumen_pendukung']); // Don't update dokumen_pendukung if no new file uploaded
            }

            $cuti->update($data);
            
            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Data cuti berhasil diperbarui.');
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
            $cuti = Cuti::findOrFail($id);
            
            // Delete file if exists
            if ($cuti->dokumen_pendukung) {
                $path = public_path('storage/' . $cuti->dokumen_pendukung);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $cuti->delete();
            
            return response()->json(['success' => true, 'message' => 'Data cuti berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}