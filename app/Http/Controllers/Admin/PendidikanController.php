<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendidikan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PendidikanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pendidikans = Pendidikan::with('pegawai')->select('pendidikan.*');

            return DataTables::of($pendidikans)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.pendidikan.show', $row->id);
                    $editUrl = route('admin.pendidikan.edit', $row->id);
                    $deleteUrl = route('admin.pendidikan.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pendidikan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        return view('admin.pendidikan.create', compact('pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenjang' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_lulus' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_ijazah' => 'nullable|string|max:255',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_ijazah')) {
                $file = $request->file('file_ijazah');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('ijazah', 'public');
                $data['file_ijazah'] = $path;
            }

            Pendidikan::create($data);
            
            DB::commit();
            return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan berhasil ditambahkan.');
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
        $pendidikan = Pendidikan::with('pegawai')->findOrFail($id);
        return view('admin.pendidikan.show', compact('pendidikan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pendidikan = Pendidikan::findOrFail($id);
        $pegawais = Pegawai::all();
        return view('admin.pendidikan.edit', compact('pendidikan', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pendidikan = Pendidikan::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenjang' => 'required|in:SD,SMP,SMA,D3,S1,S2,S3',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_lulus' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'nomor_ijazah' => 'nullable|string|max:255',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('file_ijazah')) {
                // Delete old file if exists
                if ($pendidikan->file_ijazah) {
                    $oldPath = public_path('storage/' . $pendidikan->file_ijazah);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('file_ijazah');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('ijazah', 'public');
                $data['file_ijazah'] = $path;
            } elseif ($request->has('hapus_file') && $request->hapus_file == 1) {
                // Delete existing file if requested
                if ($pendidikan->file_ijazah) {
                    $oldPath = public_path('storage/' . $pendidikan->file_ijazah);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['file_ijazah'] = null;
            } else {
                unset($data['file_ijazah']); // Don't update file_ijazah if no new file uploaded
            }

            $pendidikan->update($data);
            
            DB::commit();
            return redirect()->route('admin.pendidikan.index')->with('success', 'Data pendidikan berhasil diperbarui.');
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
            $pendidikan = Pendidikan::findOrFail($id);
            
            // Delete file if exists
            if ($pendidikan->file_ijazah) {
                $path = public_path('storage/' . $pendidikan->file_ijazah);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $pendidikan->delete();
            
            return response()->json(['success' => true, 'message' => 'Data pendidikan berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}