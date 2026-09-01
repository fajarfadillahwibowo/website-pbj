<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'data_semen';
    protected $primaryKey = 'kode_semen';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_semen',
        'nama_produk',
        'jenis_semen',
        'kemasan',
        'berat_kg',
        'harga_jual_default',
        'harga_beli_default',
        'stok_total',
    ];
}
