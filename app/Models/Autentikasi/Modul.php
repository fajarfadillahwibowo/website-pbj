<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    use HasFactory;

    protected $table = 'modul';
    protected $primaryKey = 'id_modul';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'kode_modul',
        'nama_modul',
        'kategori_modul',
        'deskripsi',
    ];

    public function hakAkses()
    {
        return $this->hasMany(HakAksesJabatan::class, 'id_modul', 'id_modul');
    }
}
