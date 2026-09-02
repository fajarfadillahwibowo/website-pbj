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
        'nomor_jurnal',
        'tanggal_transaksi',
        'kode_akun',
        'posisi',
        'nominal',
        'keterangan',
        'referensi_transaksi',
        'dibuat_oleh',
    ];

    public function akun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun', 'kode_akun');
    }
}
