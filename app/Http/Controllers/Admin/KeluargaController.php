<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class KeluargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $keluargas = Keluarga::with('pegawai')->select('keluarga.*');

            return DataTables::of($keluargas)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.keluarga.show', $row->id);
                    $editUrl = route('admin.keluarga.edit', $row->id);
                    $deleteUrl = route('admin.keluarga.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('jenis_kelamin', function($row) {
                    return $row->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.keluarga.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        return view('admin.keluarga.create', compact('pegawais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak Kandung,Ayah,Ibu',
            'nik' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'pekerjaan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            Keluarga::create($request->all());
            
            DB::commit();
            return redirect()->route('admin.keluarga.index')->with('success', 'Data keluarga berhasil ditambahkan.');
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
        $keluarga = Keluarga::with('pegawai')->findOrFail($id);
        return view('admin.keluarga.show', compact('keluarga'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $keluarga = Keluarga::findOrFail($id);
        $pegawais = Pegawai::all();
        return view('admin.keluarga.edit', compact('keluarga', 'pegawais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $keluarga = Keluarga::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|in:Suami,Istri,Anak Kandung,Ayah,Ibu',
            'nik' => 'nullable|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'jenis_kelamin' => 'required|in:L,P',
            'pekerjaan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $keluarga->update($request->all());
            
            DB::commit();
            return redirect()->route('admin.keluarga.index')->with('success', 'Data keluarga berhasil diperbarui.');
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
            $keluarga = Keluarga::findOrFail($id);
            $keluarga->delete();
            
            return response()->json(['success' => true, 'message' => 'Data keluarga berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}