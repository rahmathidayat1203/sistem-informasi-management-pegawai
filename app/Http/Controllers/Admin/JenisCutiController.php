<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class JenisCutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jenisCutis = JenisCuti::select('*');

            return DataTables::of($jenisCutis)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.jenis_cuti.show', $row->id);
                    $editUrl = route('admin.jenis_cuti.edit', $row->id);
                    $deleteUrl = route('admin.jenis_cuti.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.jenis_cuti.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.jenis_cuti.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_cuti,nama',
        ]);

        DB::beginTransaction();
        try {
            JenisCuti::create($request->all());
            DB::commit();
            return redirect()->route('admin.jenis_cuti.index')->with('success', 'Jenis cuti berhasil ditambahkan.');
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
        $jenisCuti = JenisCuti::findOrFail($id);
        return view('admin.jenis_cuti.show', compact('jenisCuti'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jenisCuti = JenisCuti::findOrFail($id);
        return view('admin.jenis_cuti.edit', compact('jenisCuti'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jenisCuti = JenisCuti::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255|unique:jenis_cuti,nama,' . $id,
        ]);

        DB::beginTransaction();
        try {
            $jenisCuti->update($request->all());
            DB::commit();
            return redirect()->route('admin.jenis_cuti.index')->with('success', 'Jenis cuti berhasil diperbarui.');
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
            $jenisCuti = JenisCuti::findOrFail($id);
            $jenisCuti->delete();
            return response()->json(['success' => true, 'message' => 'Jenis cuti berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}