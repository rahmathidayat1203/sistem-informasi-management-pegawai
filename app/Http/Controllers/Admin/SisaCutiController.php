<?php

namespace App\Http\Controllers\Admin;

use App\Models\SisaCuti;
use App\Models\Pegawai;
use App\Http\Controllers\Controller; // Import base Controller
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\DataTables; // Import Yajra DataTables
use Illuminate\Support\Facades\Validator; // Untuk validasi
use Barryvdh\DomPDF\facade\Pdf; // Import PDF

class SisaCutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Jika permintaan datang dari DataTables (ajax), kembalikan data menggunakan Yajra
        if ($request->ajax()) {
            $query = SisaCuti::select('sisa_cuti.*', 'pegawai.nama_lengkap as nama_pegawai') // Join untuk nama
                ->leftJoin('pegawai', 'sisa_cuti.pegawai_id', '=', 'pegawai.id');

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
                ->addColumn('action', function($sisaCuti) {
                    $actionBtn = '<a href="' . route('admin.sisa_cuti.edit', $sisaCuti->id) . '" class="btn btn-sm btn-warning mr-1">Edit</a>';
                    $actionBtn .= '<button class="btn btn-sm btn-danger" onclick="deleteData(\''. route('admin.sisa_cuti.destroy', $sisaCuti->id) .'\')">Hapus</button>';
                    return $actionBtn;
                })
                ->rawColumns(['action']) // Render kolom action sebagai HTML
                ->make(true); // Kembalikan response JSON untuk DataTables
        }

        // Jika permintaan biasa, kembalikan view untuk menampilkan halaman index
        return view('admin.sisa_cuti.index'); // Sesuaikan path view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Ambil daftar pegawai untuk dropdown
        $pegawaiList = Pegawai::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();
        return view('admin.sisa_cuti.create', compact('pegawaiList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'pegawai_id' => 'required|exists:pegawai,id',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5), // Tahun masuk akal
            'jatah_cuti' => 'required|integer|min:0',
            'sisa_cuti' => 'required|integer|min:0|max:jatah_cuti', // Sisa cuti tidak boleh melebihi jatah
        ]);

        // Jika validasi gagal, kembali ke halaman create dengan error
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek apakah sudah ada entri untuk pegawai_id dan tahun yang sama
        $existingSisaCuti = SisaCuti::where('pegawai_id', $request->pegawai_id)
            ->where('tahun', $request->tahun)
            ->first();

        if ($existingSisaCuti) {
            // Jika sudah ada, kembalikan error
            return redirect()->back()
                ->withErrors(['pegawai_id' => 'Sisa cuti untuk pegawai ini dan tahun ini sudah ada. Silakan edit entri yang sudah ada atau pilih tahun berbeda.'])
                ->withInput();
        }


        // Buat record baru
        SisaCuti::create($request->only(['pegawai_id', 'tahun', 'jatah_cuti', 'sisa_cuti']));

        return redirect()->route('admin.sisa_cuti.index')->with('success', 'Data sisa cuti berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * (Optional jika tidak perlu detail view)
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // Temukan data sisa_cuti berdasarkan ID
        $sisaCuti = SisaCuti::findOrFail($id);

        // Ambil daftar pegawai untuk dropdown
        $pegawaiList = Pegawai::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();

        return view('admin.sisa_cuti.edit', compact('sisaCuti', 'pegawaiList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // Temukan data sisa_cuti yang akan diupdate
        $sisaCuti = SisaCuti::findOrFail($id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'pegawai_id' => 'required|exists:pegawai,id',
            'tahun' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'jatah_cuti' => 'required|integer|min:0',
            'sisa_cuti' => 'required|integer|min:0|max:jatah_cuti',
        ]);

        // Jika validasi gagal, kembali ke halaman edit dengan error
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Cek apakah pegawai_id atau tahun berubah, dan jika ya, cek apakah kombinasi baru sudah ada
        if ($sisaCuti->pegawai_id != $request->pegawai_id || $sisaCuti->tahun != $request->tahun) {
            $existingSisaCuti = SisaCuti::where('pegawai_id', $request->pegawai_id)
                ->where('tahun', $request->tahun)
                ->where('id', '!=', $id) // Abaikan record saat ini
                ->first();

            if ($existingSisaCuti) {
                // Jika sudah ada entri lain dengan kombinasi pegawai_id dan tahun ini, kembalikan error
                return redirect()->back()
                    ->withErrors(['pegawai_id' => 'Sisa cuti untuk pegawai ini dan tahun ini sudah ada. Silakan pilih tahun berbeda atau edit entri yang sudah ada.'])
                    ->withInput();
            }
        }


        // Update record
        $sisaCuti->update($request->only(['pegawai_id', 'tahun', 'jatah_cuti', 'sisa_cuti']));

        return redirect()->route('admin.sisa_cuti.index')->with('success', 'Data sisa cuti berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // Temukan data sisa_cuti
        $sisaCuti = SisaCuti::findOrFail($id);

        // Hapus data
        $sisaCuti->delete();

        return redirect()->route('admin.sisa_cuti.index')->with('success', 'Data sisa cuti berhasil dihapus.');
    }

    /**
     * Export sisa cuti data to PDF
     */
    public function exportPdf(Request $request)
    {
        $sisaCutis = SisaCuti::with('pegawai')
            ->when($request->pegawai_id, function($query) use ($request) {
                $query->where('pegawai_id', $request->pegawai_id);
            })
            ->when($request->tahun, function($query) use ($request) {
                $query->where('tahun', $request->tahun);
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('pegawai_id')
            ->get();

        $pdf = PDF::loadView('admin.sisa_cuti.pdf', compact('sisaCutis'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download('data_sisa_cuti_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
