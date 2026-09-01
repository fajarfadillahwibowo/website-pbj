<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HakAksesJabatan extends Model
{
    use HasFactory;

    protected $table = 'hak_akses_jabatan';

    protected $fillable = [
        'jabatan_id',
        'nama_modul',
        'bisa_baca',
        'bisa_tambah',
        'bisa_ubah',
        'bisa_hapus',
    ];

    protected $casts = [
        'bisa_baca' => 'boolean',
        'bisa_tambah' => 'boolean',
        'bisa_ubah' => 'boolean',
        'bisa_hapus' => 'boolean',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
}
