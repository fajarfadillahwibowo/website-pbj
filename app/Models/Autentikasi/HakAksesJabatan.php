<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAksesJabatan extends Model
{
    use HasFactory;

    protected $table = 'hak_akses_jabatan';
    protected $primaryKey = 'id_hak_akses';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'id_jabatan',
        'id_modul',
        'boleh_lihat',
        'boleh_tambah',
        'boleh_edit',
        'boleh_hapus',
    ];

    protected $casts = [
        'boleh_lihat' => 'boolean',
        'boleh_tambah' => 'boolean',
        'boleh_edit' => 'boolean',
        'boleh_hapus' => 'boolean',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'id_modul', 'id_modul');
    }
}
