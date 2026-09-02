<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokoBangunan extends Model
{
    use HasFactory;

    protected $table = 'data_toko_bangunan';
    protected $primaryKey = 'kode_toko';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_toko',
        'kode_customer',
        'kode_wilayah',
        'nama_toko_bangunan',
        'tipe_lokasi',
        'penanggung_jawab',
        'no_hp_toko',
        'alamat_lengkap',
        'titik_koordinat',
        'status_toko',
    ];

    /**
     * Relasi ke entitas Customer Induk (Pemilik & Finansial)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    /**
     * Relasi ke Wilayah Zonasi Pengiriman
     */
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'kode_wilayah', 'kode_wilayah');
    }
}
