<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';

    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
    ];

    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'jabatan_id');
    }

    public function hakAkses()
    {
        return $this->hasMany(HakAksesJabatan::class, 'jabatan_id');
    }
}
