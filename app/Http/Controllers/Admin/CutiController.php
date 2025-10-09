<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Pegawai;
use App\Models\JenisCuti;
use App\Models\SisaCuti;
use App\Models\User;
use App\Notifications\CutiApprovalRequired;
use App\Notifications\CutiApproved;
use App\Notifications\CutiRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class CutiController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:view cuti')->only(['index', 'show', 'sisaCuti']);
        $this->middleware('permission:create cuti')->only(['create', 'store']);
        $this->middleware('permission:edit cuti')->only(['edit', 'update']);
        $this->middleware('permission:delete cuti')->only(['destroy']);
        $this->middleware('permission:approve cuti')->only(['approvalIndex', 'approve', 'reject']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cutis = Cuti::with(['pegawai', 'jenisCuti', 'pimpinanApprover'])->select('cuti.*');

            return DataTables::of($cutis)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.cuti.show', $row->id);
                    $editUrl = route('admin.cuti.edit', $row->id);
                    $deleteUrl = route('admin.cuti.destroy', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a> ';
                    $btn .= '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('jenisCuti.nama', function($row) {
                    return $row->jenisCuti->nama ?? '-';
                })
                ->editColumn('tgl_mulai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_mulai)->format('d-m-Y');
                })
                ->editColumn('tgl_selesai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_selesai)->format('d-m-Y');
                })
                ->editColumn('status_persetujuan', function($row) {
                    $statusClass = '';
                    switch($row->status_persetujuan) {
                        case 'Diajukan':
                            $statusClass = 'warning';
                            break;
                        case 'Disetujui':
                            $statusClass = 'success';
                            break;
                        case 'Ditolak':
                            $statusClass = 'danger';
                            break;
                    }
                    return '<span class="badge bg-'.$statusClass.'">'.$row->status_persetujuan.'</span>';
                })
                ->rawColumns(['action', 'status_persetujuan'])
                ->make(true);
        }

        return view('admin.cuti.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pegawais = Pegawai::all();
        $jenisCutis = JenisCuti::all();
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.cuti.create', compact('pegawais', 'jenisCutis', 'pimpinans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tgl_pengajuan' => 'required|date',
            'tgl_mulai' => 'required|date|after_or_equal:tgl_pengajuan',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string',
            'status_persetujuan' => 'required|in:Diajukan,Disetujui,Ditolak',
            'pimpinan_approver_id' => 'nullable|exists:users,id',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $jenisCuti = JenisCuti::findOrFail($request->jenis_cuti_id);

        DB::beginTransaction();
        try {
            $data = $request->except('dokumen_pendukung');

            if ($request->hasFile('dokumen_pendukung')) {
                $file = $request->file('dokumen_pendukung');
                $path = $file->store('dokumen_cuti', 'public');
                $data['dokumen_pendukung'] = $path;
            }

            if ($this->shouldDeductSisaCuti($jenisCuti, $request->status_persetujuan)) {
                $jumlahHari = Carbon::parse($request->tgl_mulai)->diffInDays(Carbon::parse($request->tgl_selesai)) + 1;
                $data['alokasi_sisa_cuti'] = $this->deductSisaCuti((int) $request->pegawai_id, $jumlahHari);
            } else {
                $data['alokasi_sisa_cuti'] = null;
            }

            $cuti = Cuti::create($data);

            // Send notification to pimpinan if status is Diajukan
            if ($request->status_persetujuan === 'Diajukan') {
                $pimpinans = User::role('Pimpinan')->get();
                Notification::send($pimpinans, new CutiApprovalRequired($cuti));
            }

            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Data cuti berhasil ditambahkan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cuti = Cuti::with(['pegawai', 'jenisCuti', 'pimpinanApprover'])->findOrFail($id);
        return view('admin.cuti.show', compact('cuti'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cuti = Cuti::findOrFail($id);
        $pegawais = Pegawai::all();
        $jenisCutis = JenisCuti::all();
        $pimpinans = User::role('Pimpinan')->get(); // Assuming we use spatie/laravel-permission
        return view('admin.cuti.edit', compact('cuti', 'pegawais', 'jenisCutis', 'pimpinans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cuti = Cuti::findOrFail($id);
        
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'jenis_cuti_id' => 'required|exists:jenis_cuti,id',
            'tgl_pengajuan' => 'required|date',
            'tgl_mulai' => 'required|date|after_or_equal:tgl_pengajuan',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'keterangan' => 'required|string',
            'status_persetujuan' => 'required|in:Diajukan,Disetujui,Ditolak',
            'pimpinan_approver_id' => 'nullable|exists:users,id',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $jenisCuti = JenisCuti::findOrFail($request->jenis_cuti_id);

        DB::beginTransaction();
        try {
            $data = $request->except(['dokumen_pendukung', 'hapus_file']);

            if ($cuti->status_persetujuan === 'Disetujui' && !empty($cuti->alokasi_sisa_cuti)) {
                $this->restoreSisaCutiAllocation((int) $cuti->pegawai_id, $cuti->alokasi_sisa_cuti);
            }

            if ($request->hasFile('dokumen_pendukung')) {
                if ($cuti->dokumen_pendukung) {
                    $oldPath = public_path('storage/' . $cuti->dokumen_pendukung);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $file = $request->file('dokumen_pendukung');
                $path = $file->store('dokumen_cuti', 'public');
                $data['dokumen_pendukung'] = $path;
            } elseif ($request->boolean('hapus_file')) {
                if ($cuti->dokumen_pendukung) {
                    $oldPath = public_path('storage/' . $cuti->dokumen_pendukung);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $data['dokumen_pendukung'] = null;
            }

            if ($this->shouldDeductSisaCuti($jenisCuti, $request->status_persetujuan)) {
                $jumlahHari = Carbon::parse($request->tgl_mulai)->diffInDays(Carbon::parse($request->tgl_selesai)) + 1;
                $data['alokasi_sisa_cuti'] = $this->deductSisaCuti((int) $request->pegawai_id, $jumlahHari);
            } else {
                $data['alokasi_sisa_cuti'] = null;
            }

            $cuti->update($data);

            DB::commit();
            return redirect()->route('admin.cuti.index')->with('success', 'Data cuti berhasil diperbarui.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $cuti = Cuti::findOrFail($id);
            $dokumenPendukung = $cuti->dokumen_pendukung;

            if ($cuti->status_persetujuan === 'Disetujui' && !empty($cuti->alokasi_sisa_cuti)) {
                $this->restoreSisaCutiAllocation((int) $cuti->pegawai_id, $cuti->alokasi_sisa_cuti);
            }

            $cuti->delete();

            DB::commit();

            if ($dokumenPendukung) {
                $path = public_path('storage/' . $dokumenPendukung);
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            return response()->json(['success' => true, 'message' => 'Data cuti berhasil dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function sisaCuti(Pegawai $pegawai)
    {
        return response()->json($this->summarizeSisaCuti((int) $pegawai->id));
    }

    private function shouldDeductSisaCuti(JenisCuti $jenisCuti, string $status): bool
    {
        $normalized = Str::lower(trim($jenisCuti->nama));
        return $status === 'Disetujui' && Str::contains($normalized, 'tahunan');
    }

    private function deductSisaCuti(int $pegawaiId, int $jumlahHari): array
    {
        $years = $this->getRelevantYears();
        $records = SisaCuti::where('pegawai_id', $pegawaiId)
            ->whereIn('tahun', $years)
            ->orderBy('tahun')
            ->lockForUpdate()
            ->get();

        $remaining = $jumlahHari;
        $allocation = [];

        foreach ($records as $record) {
            $available = (int) $record->sisa_cuti;
            if ($available <= 0) {
                continue;
            }

            $used = min($available, $remaining);
            if ($used > 0) {
                $record->sisa_cuti = $available - $used;
                $record->save();
                $allocation[$record->tahun] = $used;
                $remaining -= $used;
            }

            if ($remaining === 0) {
                break;
            }
        }

        if ($remaining > 0) {
            foreach ($records as $record) {
                if (array_key_exists($record->tahun, $allocation)) {
                    $record->sisa_cuti += $allocation[$record->tahun];
                    $record->save();
                }
            }

            throw ValidationException::withMessages([
                'tgl_mulai' => 'Sisa cuti tahunan tidak mencukupi untuk jumlah hari yang diajukan.',
            ]);
        }

        return $allocation;
    }

    private function restoreSisaCutiAllocation(int $pegawaiId, array $allocation): void
    {
        if (empty($allocation)) {
            return;
        }

        $years = array_map('intval', array_keys($allocation));
        $records = SisaCuti::where('pegawai_id', $pegawaiId)
            ->whereIn('tahun', $years)
            ->lockForUpdate()
            ->get()
            ->keyBy('tahun');

        foreach ($allocation as $year => $days) {
            $year = (int) $year;
            $days = (int) $days;

            if ($days <= 0) {
                continue;
            }

            $record = $records->get($year);
            if ($record) {
                $record->sisa_cuti += $days;
                $record->save();
            }
        }
    }

    private function summarizeSisaCuti(int $pegawaiId): array
    {
        $years = $this->getRelevantYears();
        $records = SisaCuti::where('pegawai_id', $pegawaiId)
            ->whereIn('tahun', $years)
            ->select('tahun', 'sisa_cuti')
            ->get()
            ->keyBy('tahun');

        $perYear = [];
        foreach ($years as $year) {
            $record = $records->get($year);
            $perYear[$year] = $record ? (int) $record->sisa_cuti : 0;
        }

        return [
            'total' => array_sum($perYear),
            'per_year' => $perYear,
        ];
    }

    /**
     * Display cuti approval list for pimpinan.
     */
    public function approvalIndex(Request $request)
    {
        if ($request->ajax()) {
            $cutis = Cuti::with(['pegawai', 'jenisCuti'])
                ->where('status_persetujuan', 'Diajukan')
                ->select('cuti.*');

            return DataTables::of($cutis)
                ->addColumn('action', function ($row) {
                    $approveUrl = route('pimpinan.cuti.approve', $row->id);
                    $rejectUrl = route('pimpinan.cuti.reject', $row->id);
                    $btn = '<button onclick="approveCuti(\'' . $approveUrl . '\')" class="btn btn-success btn-sm">Approve</button> ';
                    $btn .= '<button onclick="rejectCuti(\'' . $rejectUrl . '\')" class="btn btn-danger btn-sm">Reject</button>';
                    return $btn;
                })
                ->editColumn('pegawai.nama_lengkap', function($row) {
                    return $row->pegawai->nama_lengkap ?? '-';
                })
                ->editColumn('jenisCuti.nama', function($row) {
                    return $row->jenisCuti->nama ?? '-';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pimpinan.cuti.approval');
    }

    /**
     * Approve a cuti request.
     */
    public function approve(Request $request, Cuti $cuti)
    {
        if ($cuti->status_persetujuan !== 'Diajukan') {
            return response()->json(['message' => 'Only pending requests can be approved'], 422);
        }

        DB::beginTransaction();
        try {
            $cuti->update([
                'status_persetujuan' => 'Disetujui',
                'pimpinan_approver_id' => auth()->id(),
            ]);

            // Send notification to pegawai
            $pegawaiUser = $cuti->pegawai->user;
            if ($pegawaiUser) {
                $pegawaiUser->notify(new CutiApproved($cuti));
            }
            
            DB::commit();
            return response()->json(['message' => 'Cuti approved successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to approve cuti'], 500);
        }
    }

    /**
     * Reject a cuti request.
     */
    public function reject(Request $request, Cuti $cuti)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255'
        ]);

        if ($cuti->status_persetujuan !== 'Diajukan') {
            return response()->json(['message' => 'Only pending requests can be rejected'], 422);
        }

        DB::beginTransaction();
        try {
            $cuti->update([
                'status_persetujuan' => 'Ditolak',
                'pimpinan_approver_id' => auth()->id(),
                'keterangan' => $cuti->keterangan . "\n\nAlasan Penolakan: " . $request->alasan_penolakan,
            ]);

            // Restore sisa cuti since cuti is rejected
            $this->restoreSisaCuti($cuti);

            // Send notification to pegawai
            $pegawaiUser = $cuti->pegawai->user;
            if ($pegawaiUser) {
                $pegawaiUser->notify(new CutiRejected($cuti));
            }
            
            DB::commit();
            return response()->json(['message' => 'Cuti rejected successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to reject cuti'], 500);
        }
    }

    /**
     * Display my cuti requests for pegawai.
     */
    public function myCutis(Request $request)
    {
        $pegawai = auth()->user()->pegawai;
        
        if ($request->ajax()) {
            $cutis = Cuti::with(['jenisCuti', 'pimpinanApprover'])
                ->where('pegawai_id', $pegawai->id)
                ->select('cuti.*');

            return DataTables::of($cutis)
                ->addColumn('action', function ($row) {
                    $showUrl = route('admin.cuti.show', $row->id);
                    $btn = '<a href="' . $showUrl . '" class="btn btn-info btn-sm">Detail</a>';
                    
                    // Allow edit/delete only if still pending
                    if ($row->status_persetujuan === 'Diajukan') {
                        $editUrl = route('admin.cuti.edit', $row->id);
                        $deleteUrl = route('admin.cuti.destroy', $row->id);
                        $btn .= ' <a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a>';
                        $btn .= ' <button onclick="deleteData(\'' . $deleteUrl . '\')" class="btn btn-danger btn-sm">Delete</button>';
                    }
                    return $btn;
                })
                ->editColumn('jenisCuti.nama', function($row) {
                    return $row->jenisCuti->nama ?? '-';
                })
                ->editColumn('tgl_mulai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_mulai)->format('d-m-Y');
                })
                ->editColumn('tgl_selesai', function($row) {
                    return \Carbon\Carbon::parse($row->tgl_selesai)->format('d-m-Y');
                })
                ->editColumn('status_persetujuan', function($row) {
                    $statusClass = '';
                    switch($row->status_persetujuan) {
                        case 'Diajukan':
                            $statusClass = 'warning';
                            break;
                        case 'Disetujui':
                            $statusClass = 'success';
                            break;
                        case 'Ditolak':
                            $statusClass = 'danger';
                            break;
                    }
                    return '<span class="badge bg-'.$statusClass.'">'.$row->status_persetujuan.'</span>';
                })
                ->rawColumns(['action', 'status_persetujuan'])
                ->make(true);
        }

        return view('pegawai.cuti.my-cutis', compact('pegawai'));
    }

    private function restoreSisaCuti(Cuti $cuti)
    {
        if (!$cuti->alokasi_sisa_cuti) {
            return;
        }

        $alokasi = $cuti->alokasi_sisa_cuti;
        
        foreach ($alokasi as $tahun => $jumlah) {
            $sisaCuti = SisaCuti::where('pegawai_id', $cuti->pegawai_id)
                ->where('tahun', $tahun)
                ->first();
                
            if ($sisaCuti) {
                $sisaCuti->increment('sisa_cuti', $jumlah);
            }
        }
    }

    private function getRelevantYears(): array
    {
        $currentYear = (int) now()->year;
        return [$currentYear - 2, $currentYear - 1, $currentYear];
    }
}