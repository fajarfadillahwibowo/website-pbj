<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Master\Karyawan;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'account';
    protected $primaryKey = 'id_account';

    const CREATED_AT = 'tanggal_create';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'username',
        'password',
        'kode_karyawan',
        'id_jabatan',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
        ];
    }

    /**
     * Relasi ke master jabatan.
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    /**
     * Relasi ke master data karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'kode_karyawan', 'kode_karyawan');
    }
}
