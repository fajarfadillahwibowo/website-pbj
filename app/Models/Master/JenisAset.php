<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operasional\Kendaraan;

class JenisAset extends Model
{
    use HasFactory;

    protected $table = 'data_jenis_aset';
    protected $primaryKey = 'kode_jenis_aset';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_jenis_aset',
        'jenis_aset',
        'keterangan',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke unit armada kendaraan melalui aset finansial.
     */
    public function kendaraan()
    {
        return $this->hasManyThrough(
            Kendaraan::class,
            \App\Models\Keuangan\AsetPerusahaan::class,
            'kode_jenis_aset', // Foreign key pada data_aset
            'kode_aset',       // Foreign key pada data_kendaraan
            'kode_jenis_aset', // Local key pada data_jenis_aset
            'kode_aset'        // Local key pada data_aset
        );
    }

    /**
     * Accessor riwayat diedit relatif waktu.
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        if (!$this->diperbarui_pada) {
            return 'Baru ditambahkan';
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
}
