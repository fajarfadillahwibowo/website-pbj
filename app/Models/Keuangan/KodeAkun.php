<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    use HasFactory;

    protected $table = 'kode_akun';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori_akun',
        'posisi_normal',
    ];
}
