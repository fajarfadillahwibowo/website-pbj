<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'no_referensi',
        'tanggal_jurnal',
        'kode_akun',
        'posisi',
        'nominal',
        'keterangan',
    ];

    public function akun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun', 'kode_akun');
    }
}
