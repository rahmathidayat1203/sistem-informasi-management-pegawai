<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Pengaturan::all();
        
        // Group settings by category
        $kepala_opd = [
            'nama_kepala_opd' => Pengaturan::get('nama_kepala_opd'),
            'nip_kepala_opd' => Pengaturan::get('nip_kepala_opd'),
        ];
        
        $informasi_opd = [
            'nama_daerah' => Pengaturan::get('nama_daerah'),
            'nama_opd' => Pengaturan::get('nama_opd'),
            'alamat_opd' => Pengaturan::get('alamat_opd'),
            'telepon_opd' => Pengaturan::get('telepon_opd'),
            'website_opd' => Pengaturan::get('website_opd'),
            'email_opd' => Pengaturan::get('email_opd'),
        ];
        
        return view('admin.pengaturan.index', compact('kepala_opd', 'informasi_opd', 'settings'));
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'nama_kepala_opd' => 'required|string|max:255',
            'nip_kepala_opd' => 'required|string|max:50',
            'nama_daerah' => 'required|string|max:255',
            'nama_opd' => 'required|string|max:255',
            'alamat_opd' => 'required|string|max:500',
            'telepon_opd' => 'required|string|max:50',
            'website_opd' => 'required|string|max:255',
            'email_opd' => 'required|email|max:255',
        ]);

        // Update all settings
        Pengaturan::set('nama_kepala_opd', $request->nama_kepala_opd, 'Nama Kepala OPD');
        Pengaturan::set('nip_kepala_opd', $request->nip_kepala_opd, 'NIP Kepala OPD');
        Pengaturan::set('nama_daerah', $request->nama_daerah, 'Nama Daerah');
        Pengaturan::set('nama_opd', $request->nama_opd, 'Nama OPD');
        Pengaturan::set('alamat_opd', $request->alamat_opd, 'Alamat OPD');
        Pengaturan::set('telepon_opd', $request->telepon_opd, 'Telepon OPD');
        Pengaturan::set('website_opd', $request->website_opd, 'Website OPD');
        Pengaturan::set('email_opd', $request->email_opd, 'Email OPD');

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
