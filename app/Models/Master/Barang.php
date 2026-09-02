<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'data_semen';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'jenis_barang',
        'satuan_barang',
        'harga_pokok',
        'harga_jual_standar',
    ];

    protected $casts = [
        'harga_pokok' => 'decimal:2',
        'harga_jual_standar' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_pokok_rupiah',
        'harga_jual_standar_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getHargaPokokRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_pokok ?? 0, 0, ',', '.');
    }

    public function getHargaJualStandarRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual_standar ?? 0, 0, ',', '.');
    }

    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return 'Baru dibuat';
        return $waktu->locale('id')->diffForHumans();
    }

    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return '-';
        return $waktu->format('d/m/Y H:i:s');
    }
}
