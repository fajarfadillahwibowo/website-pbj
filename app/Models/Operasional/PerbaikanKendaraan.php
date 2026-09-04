<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\Kendaraan;

class PerbaikanKendaraan extends Model
{
    use HasFactory;

    protected $table = 'perbaikan_kendaraan';
    protected $primaryKey = 'id_perbaikan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_spk_perbaikan',
        'kode_kendaraan',
        'tanggal_masuk',
        'tanggal_selesai',
        'keluhan_kerusakan',
        'tindakan_perbaikan',
        'biaya_jasa',
        'biaya_sparepart',
        'total_biaya',
        'bengkel_pelaksana',
        'status_perbaikan',
        'pengawas_kendaraan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
        'biaya_jasa' => 'decimal:2',
        'biaya_sparepart' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'biaya_jasa_rupiah',
        'biaya_sparepart_rupiah',
        'total_biaya_rupiah',
        'tanggal_masuk_format',
        'tanggal_selesai_format',
        'status_badge',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke Master Armada Kendaraan (data_kendaraan)
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kode_kendaraan', 'kode_kendaraan');
    }

    /**
     * Accessor Format Rupiah Biaya Jasa
     */
    public function getBiayaJasaRupiahAttribute()
    {
        return 'Rp ' . number_format($this->biaya_jasa ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Format Rupiah Biaya Sparepart
     */
    public function getBiayaSparepartRupiahAttribute()
    {
        return 'Rp ' . number_format($this->biaya_sparepart ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Format Rupiah Total Biaya Servis
     */
    public function getTotalBiayaRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Format Tanggal Masuk
     */
    public function getTanggalMasukFormatAttribute()
    {
        if (!$this->tanggal_masuk) {
            return '-';
        }
        return \Carbon\Carbon::parse($this->tanggal_masuk)->format('d/m/Y');
    }

    /**
     * Accessor Format Tanggal Selesai
     */
    public function getTanggalSelesaiFormatAttribute()
    {
        if (!$this->tanggal_selesai) {
            return '-';
        }
        return \Carbon\Carbon::parse($this->tanggal_selesai)->format('d/m/Y');
    }

    /**
     * Accessor Badge Status Perbaikan
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_perbaikan) {
            'Selesai' => [
                'label' => 'Selesai Servis',
                'warna' => 'emerald',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
            ],
            'Dalam Proses' => [
                'label' => 'Dalam Pengerjaan',
                'warna' => 'blue',
                'bg' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
            ],
            'Menunggu Sparepart' => [
                'label' => 'Menunggu Part',
                'warna' => 'amber',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
            ],
            'Dibatalkan' => [
                'label' => 'Dibatalkan',
                'warna' => 'rose',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
            ],
            default => [
                'label' => $this->status_perbaikan ?? 'Proses',
                'warna' => 'slate',
                'bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200',
            ],
        };
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
     * Accessor Format Waktu Presisi
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
