<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetPerusahaan extends Model
{
    use HasFactory;

    protected $table = 'data_aset';
    protected $primaryKey = 'kode_aset';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_aset',
        'kode_jenis_aset',
        'nama_aset',
        'tanggal_pembelian',
        'harga_aset',
        'no_polisi',
        'no_mesin',
        'no_rangka',
        'merek_aset',
        'muatan',
        'jenis_kendaraan',
        'tahun_pembuatan',
        'tanggal_kir',
        'tanggal_pajak',
        'status_aset',
        'nama_pemilik',
    ];
}
