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

    protected $casts = [
        'harga_barang' => 'decimal:2',
        'stok_tersedia' => 'integer',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_barang_rupiah',
        'total_nilai_stok',
        'total_nilai_stok_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'status_stok',
    ];

    /**
     * Relasi ke Master Data Semen (Barang)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Accessor harga semen format Rupiah
     */
    public function getHargaBarangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_barang ?? 0, 0, ',', '.');
    }

    /**
     * Accessor kalkulasi total nilai valuasi stok semen di gudang ini
     */
    public function getTotalNilaiStokAttribute()
    {
        return (float) ($this->harga_barang ?? 0) * (int) ($this->stok_tersedia ?? 0);
    }

    /**
     * Accessor total nilai stok format Rupiah
     */
    public function getTotalNilaiStokRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_nilai_stok, 0, ',', '.');
    }

    /**
     * Accessor riwayat diedit relatif waktu
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->diperbarui_pada) {
            return 'Baru didaftarkan';
        }
        return $this->diperbarui_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor tanggal jam riwayat diedit presisi
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        if (!$this->diperbarui_pada) {
            return '-';
        }
        return $this->diperbarui_pada->format('d/m/Y H:i:s');
    }

    /**
     * Accessor status kuantitas stok semen
     */
    public function getStatusStokAttribute()
    {
        $stok = $this->stok_tersedia ?? 0;
        if ($stok <= 1000) {
            return [
                'label' => 'Kritis / Menipis',
                'warna' => 'rose',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
            ];
        } elseif ($stok <= 10000) {
            return [
                'label' => 'Sedang / Normal',
                'warna' => 'amber',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
            ];
        }
        return [
            'label' => 'Melimpah / Aman',
            'warna' => 'emerald',
            'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
        ];
    }
}
