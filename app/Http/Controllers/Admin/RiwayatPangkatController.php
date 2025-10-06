<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiwayatPangkat;
use App\Models\Pegawai;
use App\Models\Golongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RiwayatPangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $riwayatPangkats = RiwayatPangkat::with(['pegawai', 'golongan'])->select('riwayat_pangkat.*');

            return DataTables::of($riwayatPangkats)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.riwayat_pangkat.show', $row->id);
                    $editUrl = route('admin.riwayat_pangkat.edit', $row->id);
                    $deleteUrl = route('admin.riwayat_pangkat.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('golongan.nama', function($row) {
                    return $row->golongan->nama ?? '-';
                })
                ->editColumn('tanggal_sk', function($row) {
                    return \Carbon\Carbon::parse($row->tanggal_sk)->format('d-m-Y');
                })
                ->editColumn('tmt_pangkat', function($row) {
                    return \Carbon\Carbon::parse($row->tmt_pangkat)->format('d-m-Y');
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.riwayat_pangkat.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        $golongans = Golongan::all();
        return view('admin.riwayat_pangkat.create', compact('pegawais', 'golongans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'golongan_id' => 'required|exists:golongan,id',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tmt_pangkat' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_sk')) {
                $file = $request->file('file_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('sk_pangkat', 'public');
                $data['file_sk'] = $path;
            }

            RiwayatPangkat::create($data);
            
            DB::commit();
            return redirect()->route('admin.riwayat_pangkat.index')->with('success', 'Data riwayat pangkat berhasil ditambahkan.');
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
        $riwayatPangkat = RiwayatPangkat::with(['pegawai', 'golongan'])->findOrFail($id);
        return view('admin.riwayat_pangkat.show', compact('riwayatPangkat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $riwayatPangkat = RiwayatPangkat::findOrFail($id);
        $pegawais = Pegawai::all();
        $golongans = Golongan::all();
        return view('admin.riwayat_pangkat.edit', compact('riwayatPangkat', 'pegawais', 'golongans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $riwayatPangkat = RiwayatPangkat::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'golongan_id' => 'required|exists:golongan,id',
            'nomor_sk' => 'required|string|max:255',
            'tanggal_sk' => 'required|date',
            'tmt_pangkat' => 'required|date',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_sk')) {
                // Delete old file if exists
                if ($riwayatPangkat->file_sk) {
                    $oldPath = public_path('storage/' . $riwayatPangkat->file_sk);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('file_sk');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('sk_pangkat', 'public');
                $data['file_sk'] = $path;
            } elseif ($request->has('hapus_file') && $request->hapus_file == 1) {
                // Delete existing file if requested
                if ($riwayatPangkat->file_sk) {
                    $oldPath = public_path('storage/' . $riwayatPangkat->file_sk);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['file_sk'] = null;
            } else {
                unset($data['file_sk']); // Don't update file_sk if no new file uploaded
            }

            $riwayatPangkat->update($data);
            
            DB::commit();
            return redirect()->route('admin.riwayat_pangkat.index')->with('success', 'Data riwayat pangkat berhasil diperbarui.');
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
            $riwayatPangkat = RiwayatPangkat::findOrFail($id);
            
            // Delete file if exists
            if ($riwayatPangkat->file_sk) {
                $path = public_path('storage/' . $riwayatPangkat->file_sk);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $riwayatPangkat->delete();
            
            return response()->json(['success' => true, 'message' => 'Data riwayat pangkat berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}