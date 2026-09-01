<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianSparepart extends Model
{
    use HasFactory;

    protected $table = 'pembelian_sparepart';
    protected $primaryKey = 'id_pembelian_part';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nomor_faktur_beli',
        'kode_sparepart',
        'tanggal_beli',
        'nama_supplier',
        'jumlah_beli',
        'harga_beli',
        'total_bayar',
        'dibuat_oleh',
    ];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'kode_sparepart', 'kode_sparepart');
    }
}
