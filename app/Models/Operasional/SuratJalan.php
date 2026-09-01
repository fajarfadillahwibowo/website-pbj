<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Karyawan;
use App\Models\Keuangan\PembelianSO;
use App\Models\Keuangan\AsetPerusahaan;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_surat_jalan',
        'id_so',
        'kode_aset',
        'kode_driver',
        'tanggal_kirim',
        'status_pengiriman',
        'keterangan',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(PembelianSO::class, 'id_so', 'id_so');
    }

    public function driver()
    {
        return $this->belongsTo(Karyawan::class, 'kode_driver', 'kode_karyawan');
    }

    public function kendaraan()
    {
        return $this->belongsTo(AsetPerusahaan::class, 'kode_aset', 'kode_aset');
    }
}
