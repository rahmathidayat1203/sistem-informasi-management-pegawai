<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatJabatan;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\facade\Pdf;

class RiwayatJabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $riwayatJabatans = RiwayatJabatan::with(['pegawai', 'jabatan', 'unitKerja'])->select('riwayat_jabatan.*');

            return DataTables::of($riwayatJabatans)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.riwayat_jabatan.show', $row->id);
                    $editUrl = route('admin.riwayat_jabatan.edit', $row->id);
                    $deleteUrl = route('admin.riwayat_jabatan.destroy', $row->id);
                    $exportUrl = route('admin.riwayat_jabatan.export.single.pdf', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<a href="' . $exportUrl . '" class="btn btn-warning btn-sm me-1" title="Export PDF">
                                <i class="fas fa-file-pdf"></i> PDF
                              </a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('jabatan.nama', function($row) {
                    return $row->jabatan->nama ?? '-';
                })
                ->editColumn('unitKerja.nama', function($row) {
                    return $row->unitKerja->nama ?? '-';
                })
                ->editColumn('tanggal_sk', function($row) {
                    return \Carbon\Carbon::parse($row->tanggal_sk)->format('d-m-Y');
                })
                ->editColumn('tmt_jabatan', function($row) {
                    return \Carbon\Carbon::parse($row->tmt_jabatan)->format('d-m-Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.riwayat_jabatan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        $jabatans = Jabatan::all();
        $unitKerjas = UnitKerja::all();
        return view('admin.riwayat_jabatan.create', compact('pegawais', 'jabatans', 'unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'jenis_jabatan' => 'required|in:Struktural,Fungsional Tertentu,Fungsional Umum',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tmt_jabatan' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_sk')) {
                $file = $request->file('file_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('sk_jabatan', 'public');
                $data['file_sk'] = $path;
            }

            RiwayatJabatan::create($data);
            
            DB::commit();
            return redirect()->route('admin.riwayat_jabatan.index')->with('success', 'Data riwayat jabatan berhasil ditambahkan.');
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
        $riwayatJabatan = RiwayatJabatan::with(['pegawai', 'jabatan', 'unitKerja'])->findOrFail($id);
        return view('admin.riwayat_jabatan.show', compact('riwayatJabatan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $riwayatJabatan = RiwayatJabatan::findOrFail($id);
        $pegawais = Pegawai::all();
        $jabatans = Jabatan::all();
        $unitKerjas = UnitKerja::all();
        return view('admin.riwayat_jabatan.edit', compact('riwayatJabatan', 'pegawais', 'jabatans', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $riwayatJabatan = RiwayatJabatan::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jabatan_id' => 'required|exists:jabatan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'jenis_jabatan' => 'required|in:Struktural,Fungsional Tertentu,Fungsional Umum',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tmt_jabatan' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_sk')) {
                // Delete old file if exists
                if ($riwayatJabatan->file_sk) {
                    $oldPath = public_path('storage/' . $riwayatJabatan->file_sk);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('file_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('sk_jabatan', 'public');
                $data['file_sk'] = $path;
            } elseif ($request->has('hapus_file') && $request->hapus_file == 1) {
                // Delete existing file if requested
                if ($riwayatJabatan->file_sk) {
                    $oldPath = public_path('storage/' . $riwayatJabatan->file_sk);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['file_sk'] = null;
            } else {
                unset($data['file_sk']); // Don't update file_sk if no new file uploaded
            }

            $riwayatJabatan->update($data);
            
            DB::commit();
            return redirect()->route('admin.riwayat_jabatan.index')->with('success', 'Data riwayat jabatan berhasil diperbarui.');
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
            $riwayatJabatan = RiwayatJabatan::findOrFail($id);
            
            // Delete file if exists
            if ($riwayatJabatan->file_sk) {
                $path = public_path('storage/' . $riwayatJabatan->file_sk);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $riwayatJabatan->delete();
            
            return response()->json(['success' => true, 'message' => 'Data riwayat jabatan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Export single riwayat jabatan data to PDF
     */
    public function exportSinglePdf($id)
    {
        $riwayatJabatan = RiwayatJabatan::with(['pegawai', 'jabatan', 'unitKerja'])
            ->findOrFail($id);

        $pdf = PDF::loadView('admin.riwayat_jabatan.single_pdf', compact('riwayatJabatan'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('riwayat_jabatan_' . $riwayatJabatan->pegawai->nama_lengkap . '_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}