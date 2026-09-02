<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranKas extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_pengeluaran',
        'tanggal_pengeluaran',
        'kategori_pengeluaran',
        'kode_akun',
        'total_nominal',
        'id_rekening_sumber',
        'keterangan',
        'status_persetujuan',
        'disetujui_oleh',
        'dibuat_oleh',
    ];

    public function akun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun', 'kode_akun');
    }
}
