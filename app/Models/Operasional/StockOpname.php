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
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'no_so',
        'no_lo',
        'tanggal',
        'nama_pemilik',
        'alamat',
        'no_hp',
        'no_ktp',
        'foto_ktp',
        'status_aset',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
