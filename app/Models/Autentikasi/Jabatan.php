<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'deskripsi',
    ];

    public function akunPengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_jabatan', 'id_jabatan');
    }

    public function hakAkses()
    {
        return $this->hasMany(HakAksesJabatan::class, 'id_jabatan', 'id_jabatan');
    }
}
