<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Autentikasi\Jabatan;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'data_karyawan';
    protected $primaryKey = 'kode_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_karyawan',
        'nama_karyawan',
        'id_jabatan',
        'kategori_karyawan',
        'no_identitas',
        'alamat',
        'no_hp',
        'foto_ktp',
        'file_kontrak',
        'status_karyawan',
        'tanggal_mulai_kerja',
        'tanggal_berhenti',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }
}
