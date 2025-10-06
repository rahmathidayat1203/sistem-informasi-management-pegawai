<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerjalananDinas;
use App\Models\User; // For pimpinan_pemberi_tugas
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PerjalananDinasController extends Controller
{
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
            'nomor_surat_tugas' => 'required|string|max:255|unique:perjalanan_dinas,nomor_surat_tugas',
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

            // Attach pegawais to this perjalanan dinas
            $perjalananDinas->pegawai()->attach($request->pegawai_ids);

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
}