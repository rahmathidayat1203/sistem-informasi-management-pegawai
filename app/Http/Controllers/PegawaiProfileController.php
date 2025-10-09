<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pendidikan;
use App\Models\Keluarga;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatJabatan;
use App\Models\Cuti;
use App\Models\PerjalananDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiProfileController extends Controller
{
    /**
     * Display the pegawai's complete profile.
     */
    public function index()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return redirect()->route('dashboard')->with('error', 'Data pegawai tidak ditemukan');
        }

        // Load complete profile data
        $pegawai->load([
            'pendidikan',
            'keluarga',
            'riwayatPangkat' => function($query) {
                $query->orderBy('tmt_pangkat', 'desc');
            },
            'riwayatJabatan' => function($query) {
                $query->orderBy('tmt_jabatan', 'desc');
            },
            'golongan',
            'jabatan',
            'unitKerja'
        ]);

        // Recent activities
        $recentCuti = Cuti::where('pegawai_id', $pegawai->id)
            ->with('jenisCuti')
            ->latest()
            ->take(5)
            ->get();

        $recentPerjalanan = PerjalananDinas::whereHas('pegawai', function($query) use ($pegawai) {
            $query->where('pegawais.id', $pegawai->id);
        })->latest()->take(5)->get();

        return view('pegawai.profile.index', compact(
            'pegawai',
            'recentCuti',
            'recentPerjalanan'
        ));
    }

    /**
     * Show edit form for personal data.
     */
    public function editPersonalData()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            abort(404);
        }

        return view('pegawai.profile.edit-personal', compact('pegawai'));
    }

    /**
     * Update personal data (read-only for pegawai, requires admin approval).
     */
    public function updatePersonalData(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            abort(404);
        }

        $request->validate([
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:20',
            'email_pribadi' => 'nullable|email|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        // Only certain fields can be updated by pegawai
        $pegawai->update([
            'alamat' => $request->alamat ?? $pegawai->alamat,
            'telepon' => $request->telepon ?? $pegawai->telepon,
            'email_pribadi' => $request->email_pribadi ?? $pegawai->email_pribadi,
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('foto_pegawai', 'public');
            
            // Delete old photo if exists
            if ($pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            
            $pegawai->foto = $path;
            $pegawai->save();
        }

        return redirect()->route('pegawai.profile.index')
            ->with('success', 'Data pribadi berhasil diperbarui. Perubahan data utama memerlukan persetujuan admin.');
    }

    /**
     * Add new pendidikan.
     */
    public function storePendidikan(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        $request->validate([
            'tingkat_pendidikan' => 'required|string|max:100',
            'nama_institusi' => 'required|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'tahun_masuk' => 'required|digits:4',
            'tahun_lulus' => 'required|digits:4|after_or_equal:tahun_masuk',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except('ijazah');
        $data['pegawai_id'] = $pegawai->id;

        if ($request->hasFile('ijazah')) {
            $file = $request->file('ijazah');
            $data['ijazah'] = $file->store('ijazah', 'public');
        }

        Pendidikan::create($data);

        return redirect()->route('pegawai.profile.index')
            ->with('success', 'Data pendidikan berhasil ditambahkan.');
    }

    /**
     * Add new keluarga member.
     */
    public function storeKeluarga(Request $request)
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'hubungan' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'pekerjaan' => 'nullable|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['pegawai_id'] = $pegawai->id;

        Keluarga::create($data);

        return redirect()->route('pegawai.profile.index')
            ->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    /**
     * Download document.
     */
    public function downloadDocument($type, $id)
    {
        $user = Auth::user();
        
        switch ($type) {
            case 'ijazah':
                $document = Pendidikan::where('pegawai_id', $user->pegawai->id)
                    ->where('id', $id)
                    ->firstOrFail();
                $path = $document->ijazah;
                $filename = "ijazah_{$user->pegawai->nama_lengkap}_{$document->nama_institusi}.pdf";
                break;
                
            case 'foto':
                $path = $user->pegawai->foto;
                $filename = "foto_{$user->pegawai->nama_lengkap}.jpg";
                break;
                
            default:
                abort(404);
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path, $filename);
    }

    /**
     * View statistics dashboard for pegawai.
     */
    public function statistics()
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            abort(404);
        }

        // Cuti statistics
        $cutiStats = [
            'total_diajukan' => Cuti::where('pegawai_id', $pegawai->id)->count(),
            'disetujui' => Cuti::where('pegawai_id', $pegawai->id)
                ->where('status_persetujuan', 'Disetujui')->count(),
            'ditolak' => Cuti::where('pegawai_id', $pegawai->id)
                ->where('status_persetujuan', 'Ditolak')->count(),
            'menunggu' => Cuti::where('pegawai_id', $pegawai->id)
                ->where('status_persetujuan', 'Diajukan')->count(),
        ];

        // Perjalanan dinas statistics
        $perjalananStats = PerjalananDinas::whereHas('pegawai', function($query) use ($pegawai) {
            $query->where('pegawais.id', $pegawai->id);
        })->get();

        return view('pegawai.profile.statistics', compact(
            'pegawai',
            'cutiStats',
            'perjalananStats'
        ));
    }
}
