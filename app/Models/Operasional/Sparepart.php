<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $table = 'list_sparepart';
    protected $primaryKey = 'kode_sparepart';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_sparepart',
        'nama_sparepart',
        'kategori_part',
        'stok_part',
        'satuan',
        'harga_satuan',
    ];
}
