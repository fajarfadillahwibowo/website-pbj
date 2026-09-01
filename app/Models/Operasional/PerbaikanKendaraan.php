<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Keuangan\AsetPerusahaan;

class PerbaikanKendaraan extends Model
{
    use HasFactory;

    protected $table = 'perbaikan_kendaraan';
    protected $primaryKey = 'id_perbaikan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_spk_perbaikan',
        'kode_aset',
        'tanggal_masuk',
        'tanggal_selesai',
        'keluhan_kerusakan',
        'tindakan_perbaikan',
        'biaya_jasa',
        'biaya_sparepart',
        'total_biaya',
        'bengkel_pelaksana',
        'status_perbaikan',
        'pengawas_kendaraan',
    ];

    public function kendaraan()
    {
        return $this->belongsTo(AsetPerusahaan::class, 'kode_aset', 'kode_aset');
    }
}
