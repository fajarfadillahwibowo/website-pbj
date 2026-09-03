<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenyusutan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_penyusutan';
    protected $primaryKey = 'id_penyusutan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nomor_penyusutan',
        'kode_aset',
        'tanggal_penyusutan',
        'periode_bulan',
        'periode_tahun',
        'beban_penyusutan',
        'akumulasi_penyusutan',
        'nilai_buku',
        'nomor_jurnal',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_penyusutan' => 'date',
        'beban_penyusutan' => 'decimal:2',
        'akumulasi_penyusutan' => 'decimal:2',
        'nilai_buku' => 'decimal:2',
        'dibuat_pada' => 'datetime',
    ];

    /**
     * Relasi ke Aset Perusahaan.
     */
    public function aset()
    {
        return $this->belongsTo(AsetPerusahaan::class, 'kode_aset', 'kode_aset');
    }
}
