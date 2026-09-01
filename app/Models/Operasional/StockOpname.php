<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'opname_gudang';
    protected $primaryKey = 'id_opname';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nomor_opname',
        'kode_gudang',
        'tanggal_opname',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan_selisih',
        'status_konfirmasi',
        'petugas_opname',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
