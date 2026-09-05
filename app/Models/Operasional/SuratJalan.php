<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\Driver;
use App\Models\Operasional\Kendaraan;
use App\Models\Keuangan\PembelianSO;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_surat_jalan',
        'id_so',
        'jumlah_zak',
        'kode_kendaraan',
        'kode_driver',
        'tanggal_kirim',
        'status_pengiriman',
        'disetujui_oleh',
        'disetujui_pada',
        'alasan_penolakan',
        'status_penerimaan_gudang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'datetime',
        'disetujui_pada' => 'datetime',
        'jumlah_zak' => 'integer',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'tanggal_kirim_format',
        'status_badge',
    ];

    /**
     * Relasi ke Sales Order semen.
     */
    public function salesOrder()
    {
        return $this->belongsTo(PembelianSO::class, 'id_so', 'id_so');
    }

    /**
     * Relasi ke Driver / Supir (Data Karyawan).
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'kode_driver', 'kode_karyawan');
    }

    /**
     * Relasi ke Kendaraan / Truk Armada (Data Kendaraan).
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kode_kendaraan', 'kode_kendaraan');
    }

    /**
     * Accessor riwayat diedit relatif waktu.
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->diperbarui_pada) {
            return 'Baru diterbitkan';
        }
        return $this->diperbarui_pada->locale('id')->diffForHumans();
    }

    /**
     * Accessor tanggal jam riwayat diedit presisi.
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        if (!$this->diperbarui_pada) {
            return '-';
        }
        return $this->diperbarui_pada->format('d/m/Y H:i:s');
    }

    /**
     * Accessor format tanggal jam pengiriman.
     */
    public function getTanggalKirimFormatAttribute()
    {
        if (!$this->tanggal_kirim) {
            return '-';
        }
        return $this->tanggal_kirim->format('d/m/Y H:i');
    }

    /**
     * Accessor badge warna dan label status pengiriman.
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_pengiriman) {
            'dalam_perjalanan' => [
                'label' => 'Dalam Perjalanan',
                'warna' => 'blue',
                'bg' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
            ],
            'terkirim' => [
                'label' => 'Terkirim / Selesai',
                'warna' => 'emerald',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
            ],
            'ditolak' => [
                'label' => 'Ditolak / Perlu Revisi',
                'warna' => 'rose',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
            ],
            'retur' => [
                'label' => 'Retur Pengiriman',
                'warna' => 'purple',
                'bg' => 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/20',
            ],
            default => [
                'label' => 'Menunggu Persetujuan SPV',
                'warna' => 'amber',
                'bg' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
            ],
        };
    }
}
