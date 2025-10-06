<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UnitKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $unitKerjas = UnitKerja::select('*');

            return DataTables::of($unitKerjas)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.unit_kerja.show', $row->id);
                    $editUrl = route('admin.unit_kerja.edit', $row->id);
                    $deleteUrl = route('admin.unit_kerja.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.unit_kerja.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.unit_kerja.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:unit_kerja,nama',
            'deskripsi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            UnitKerja::create($request->all());
            DB::commit();
            return redirect()->route('admin.unit_kerja.index')->with('success', 'Unit Kerja berhasil ditambahkan.');
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
        $unitKerja = UnitKerja::findOrFail($id);
        return view('admin.unit_kerja.show', compact('unitKerja'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $unitKerja = UnitKerja::findOrFail($id);
        return view('admin.unit_kerja.edit', compact('unitKerja'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unitKerja = UnitKerja::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255|unique:unit_kerja,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $unitKerja->update($request->all());
            DB::commit();
            return redirect()->route('admin.unit_kerja.index')->with('success', 'Unit Kerja berhasil diperbarui.');
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
            $unitKerja = UnitKerja::findOrFail($id);
            $unitKerja->delete();
            return response()->json(['success' => true, 'message' => 'Unit Kerja berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}