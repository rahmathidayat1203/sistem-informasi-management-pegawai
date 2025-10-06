<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pegawai_id',
        'name',
        'email',
        'password',
        'username',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the pegawai associated with the user.
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    /**
     * Get the cuti records where this user is the approver.
     */
    public function approvedCuti()
    {
        return $this->hasMany(Cuti::class, 'pimpinan_approver_id');
    }

    /**
     * Get the perjalanan_dinas records where this user is the task giver.
     */
    public function perjalananDinasAsPimpinan()
    {
        return $this->hasMany(PerjalananDinas::class, 'pimpinan_pemberi_tugas_id');
    }

    /**
     * Get the laporan_pd records where this user is the verifier.
     */
    public function verifiedLaporanPD()
    {
        return $this->hasMany(LaporanPD::class, 'admin_keuangan_verifier_id');
    }
}
