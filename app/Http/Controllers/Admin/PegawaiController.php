<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Golongan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pegawai = Pegawai::with(['jabatan', 'golongan', 'unitKerja'])->select('pegawai.*');

            return DataTables::of($pegawai)
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.pegawai.edit', $row->id);
                    $deleteUrl = route('admin.pegawai.destroy', $row->id);
                    $btn = '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pegawai.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jabatans = Jabatan::all();
        $golongans = Golongan::all();
        $unitKerjas = UnitKerja::all();
        
        return view('admin.pegawai.create', compact('jabatans', 'golongans', 'unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'NIP' => 'required|string|unique:pegawai,NIP',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
            'jabatan_id' => 'required|exists:jabatan,id',
            'golongan_id' => 'required|exists:golongan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('foto_profil')) {
                $file = $request->file('foto_profil');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('pegawai_foto', 'public');
                $data['foto_profil'] = $path;
            }

            Pegawai::create($data);
            
            DB::commit();
            return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
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
        $pegawai = Pegawai::with(['jabatan', 'golongan', 'unitKerja'])->findOrFail($id);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $jabatans = Jabatan::all();
        $golongans = Golongan::all();
        $unitKerjas = UnitKerja::all();
        
        return view('admin.pegawai.edit', compact('pegawai', 'jabatans', 'golongans', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::findOrFail($id);
        
        $request->validate([
            'NIP' => 'required|string|unique:pegawai,NIP,' . $id,
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'alamat' => 'required|string',
            'no_telp' => 'required|string|max:20',
            'jabatan_id' => 'required|exists:jabatan,id',
            'golongan_id' => 'required|exists:golongan,id',
            'unit_kerja_id' => 'required|exists:unit_kerja,id',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('foto_profil')) {
                // Delete old file if exists
                if ($pegawai->foto_profil) {
                    $oldPath = public_path('storage/' . $pegawai->foto_profil);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('foto_profil');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->store('pegawai_foto', 'public');
                $data['foto_profil'] = $path;
            } elseif ($request->has('hapus_foto') && $request->hapus_foto == 1) {
                // Delete existing photo if requested
                if ($pegawai->foto_profil) {
                    $oldPath = public_path('storage/' . $pegawai->foto_profil);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['foto_profil'] = null;
            } else {
                unset($data['foto_profil']); // Don't update foto_profil if no new file uploaded
            }

            $pegawai->update($data);
            
            DB::commit();
            return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
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
            $pegawai = Pegawai::findOrFail($id);
            
            // Delete photo if exists
            if ($pegawai->foto_profil) {
                $path = public_path('storage/' . $pegawai->foto_profil);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            $pegawai->delete();
            
            return response()->json(['success' => true, 'message' => 'Data pegawai berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}