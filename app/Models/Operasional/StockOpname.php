<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\Gudang;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'opname_gudang';
    protected $primaryKey = 'id_opname';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null; // Tabel opname_gudang tidak memiliki kolom diperbarui_pada

    protected $fillable = [
        'nomor_opname',
        'kode_gudang',
        'tanggal_opname',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan_selisih',
        'status_konfirmasi',
        'petugas_opname',
    ];

    protected $casts = [
        'tanggal_opname' => 'date:Y-m-d',
        'stok_sistem' => 'integer',
        'stok_fisik' => 'integer',
        'selisih' => 'integer',
        'dibuat_pada' => 'datetime',
    ];

    protected $appends = [
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'tanggal_format',
        'status_badge',
        'selisih_badge',
    ];

    /**
     * Relasi ke Fasilitas Gudang (list_gudang_so)
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    /**
     * Accessor riwayat dibuat relatif waktu
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->dibuat_pada) {
            return 'Baru dibuat';
        }
        return $this->dibuat_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor format tanggal jam dibuat presisi
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        if (!$this->dibuat_pada) {
            return '-';
        }
        return $this->dibuat_pada->format('d/m/Y H:i:s');
    }

    /**
     * Accessor format tanggal opname
     */
    public function getTanggalFormatAttribute()
    {
        if (!$this->tanggal_opname) {
            return '-';
        }
        return $this->tanggal_opname->format('d/m/Y');
    }

    /**
     * Accessor status konfirmasi SPV
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_konfirmasi) {
            'dikonfirmasi_spv' => [
                'label' => 'Dikonfirmasi SPV',
                'warna' => 'emerald',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
            ],
            default => [
                'label' => 'Draft / Menunggu',
                'warna' => 'amber',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
            ],
        };
    }

    /**
     * Accessor badge selisih stok (Surplus / Minus / Cocok)
     */
    public function getSelisihBadgeAttribute()
    {
        $selisih = $this->selisih ?? 0;
        if ($selisih > 0) {
            return [
                'label' => '+' . number_format($selisih, 0, ',', '.') . ' Zak (Surplus)',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200',
            ];
        } elseif ($selisih < 0) {
            return [
                'label' => number_format($selisih, 0, ',', '.') . ' Zak (Minus)',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200',
            ];
        }
        return [
            'label' => '0 Zak (Cocok Sesuai)',
            'bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200',
        ];
    }
}
