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

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'tanggal_kir' => 'date',
        'tanggal_pajak' => 'date',
        'harga_aset' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_aset_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getHargaAsetRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_aset ?? 0, 0, ',', '.');
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
