<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\Sparepart;

class PembelianSparepart extends Model
{
    use HasFactory;

    protected $table = 'pembelian_sparepart';
    protected $primaryKey = 'id_pembelian_part';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nomor_faktur_beli',
        'kode_sparepart',
        'tanggal_beli',
        'nama_supplier',
        'jumlah_beli',
        'harga_beli',
        'total_bayar',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_beli' => 'date:Y-m-d',
        'jumlah_beli' => 'integer',
        'harga_beli' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'dibuat_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_beli_rupiah',
        'total_bayar_rupiah',
        'tanggal_beli_format',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke Master Sparepart (list_sparepart)
     */
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class, 'kode_sparepart', 'kode_sparepart');
    }

    /**
     * Accessor Harga Beli Rupiah
     */
    public function getHargaBeliRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_beli ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Total Bayar Rupiah
     */
    public function getTotalBayarRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_bayar ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Format Tanggal Beli
     */
    public function getTanggalBeliFormatAttribute()
    {
        if (!$this->tanggal_beli) {
            return '-';
        }
        return $this->tanggal_beli->format('d/m/Y');
    }

    /**
     * Accessor Riwayat Diedit Relatif
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->dibuat_pada) {
            return 'Baru dibuat';
        }
        return $this->dibuat_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor Format Waktu Presisi
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        if (!$this->dibuat_pada) {
            return '-';
        }
        return $this->dibuat_pada->format('d/m/Y H:i:s');
    }
}
