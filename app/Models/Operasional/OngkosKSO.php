<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\KSO;

class OngkosKSO extends Model
{
    use HasFactory;

    protected $table = 'ongkos_kso';
    protected $primaryKey = 'kode_oa';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_oa',
        'kode_kso',
        'nama_oa',
        'muatan',
        'ongkos_angkut',
    ];

    protected $casts = [
        'ongkos_angkut' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'ongkos_angkut_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke Data Mitra KSO (data_kso)
     */
    public function mitraKso()
    {
        return $this->belongsTo(KSO::class, 'kode_kso', 'kode_kso');
    }

    /**
     * Accessor Format Rupiah Tarif Ongkos Angkut
     */
    public function getOngkosAngkutRupiahAttribute()
    {
        return 'Rp ' . number_format($this->ongkos_angkut ?? 0, 0, ',', '.');
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
}
