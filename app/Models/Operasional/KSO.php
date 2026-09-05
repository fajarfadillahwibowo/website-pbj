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
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'file_kontrak_url',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
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
     * Accessor Total Rute OA Mitra
     */
    public function getTotalRuteOaAttribute()
    {
        return $this->daftarOngkosKso()->count();
    }
}
