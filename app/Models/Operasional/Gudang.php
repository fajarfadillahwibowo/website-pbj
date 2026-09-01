<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'list_gudang_so';
    protected $primaryKey = 'kode_gudang';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'lokasi_gudang',
        'kapasitas_zak',
        'kapasitas_curah_ton',
    ];
}
