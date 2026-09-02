<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\PembelianSparepart;

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

    protected $casts = [
        'stok_part' => 'integer',
        'harga_satuan' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_satuan_rupiah',
        'total_valuasi_rupiah',
        'status_stok',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke riwayat pembelian sparepart
     */
    public function daftarPembelian()
    {
        return $this->hasMany(PembelianSparepart::class, 'kode_sparepart', 'kode_sparepart');
    }

    /**
     * Accessor Harga Satuan Rupiah
     */
    public function getHargaSatuanRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Total Valuasi Nilai Stok Suku Cadang
     */
    public function getTotalValuasiRupiahAttribute()
    {
        $total = ($this->stok_part ?? 0) * ($this->harga_satuan ?? 0);
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    /**
     * Accessor Status Level Ketersediaan Stok
     */
    public function getStatusStokAttribute()
    {
        $stok = $this->stok_part ?? 0;
        if ($stok <= 0) {
            return [
                'label' => 'Habis (0)',
                'warna' => 'rose',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
            ];
        } elseif ($stok <= 5) {
            return [
                'label' => 'Menipis (' . $stok . ' ' . ($this->satuan ?? 'Pcs') . ')',
                'warna' => 'amber',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
            ];
        }
        return [
            'label' => 'Aman (' . $stok . ' ' . ($this->satuan ?? 'Pcs') . ')',
            'warna' => 'emerald',
            'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
        ];
    }

    /**
     * Accessor Riwayat Diedit Relatif
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return 'Baru dibuat';
        }
        return $waktu->locale('id')->diffForHumans();
    }

    /**
     * Accessor Format Tanggal & Jam Presisi
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return '-';
        }
        return $waktu->format('d/m/Y H:i:s');
    }
}
