<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Barang;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'list_gudang_so';
    protected $primaryKey = 'kode_gudang';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'jenis_gudang',
        'kode_barang',
        'plant',
        'harga_barang',
        'stok_tersedia',
        'distrik',
        'sub_distrik',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
