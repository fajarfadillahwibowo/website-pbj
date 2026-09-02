<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Operasional\OngkosKSO;

class KSO extends Model
{
    use HasFactory;

    protected $table = 'data_kso';
    protected $primaryKey = 'kode_kso';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_kso',
        'nama_kso',
        'file_kontrak_kso',
        'status_kso',
        'pihak_mitra',
        'tanggal_mulai',
        'tanggal_selesai',
        'nilai_kontrak',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_kontrak' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'file_kontrak_url',
        'nilai_kontrak_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
        'status_badge',
        'total_rute_oa',
    ];

    /**
     * Relasi ke Ongkos Angkut KSO (ongkos_kso)
     */
    public function daftarOngkosKso()
    {
        return $this->hasMany(OngkosKSO::class, 'kode_kso', 'kode_kso');
    }

    /**
     * Accessor URL File Dokumen Kontrak KSO
     */
    public function getFileKontrakUrlAttribute()
    {
        if ($this->file_kontrak_kso) {
            return Storage::url($this->file_kontrak_kso);
        }
        return null;
    }

    /**
     * Accessor Format Rupiah Nilai Kontrak
     */
    public function getNilaiKontrakRupiahAttribute()
    {
        return 'Rp ' . number_format($this->nilai_kontrak ?? 0, 0, ',', '.');
    }

    /**
     * Accessor Riwayat Terakhir Diedit Relatif
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

    /**
     * Accessor Badge Status KSO
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_kso) {
            'Aktif' => [
                'label' => 'Kontrak Aktif',
                'warna' => 'emerald',
                'bg' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
            ],
            'Selesai' => [
                'label' => 'Kontrak Selesai',
                'warna' => 'blue',
                'bg' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-500/20',
            ],
            'Ditangguhkan' => [
                'label' => 'Ditangguhkan',
                'warna' => 'rose',
                'bg' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
            ],
            default => [
                'label' => $this->status_kso ?? 'Aktif',
                'warna' => 'slate',
                'bg' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200',
            ],
        };
    }

    /**
     * Accessor Total Rute OA Mitra
     */
    public function getTotalRuteOaAttribute()
    {
        return $this->daftarOngkosKso()->count();
    }
}
