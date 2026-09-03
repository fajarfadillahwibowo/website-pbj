<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Wilayah;
use App\Models\Operasional\Gudang;

class OngkosAngkut extends Model
{
    use HasFactory;

    protected $table = 'data_ongkos_angkut';
    protected $primaryKey = 'id_ongkos';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_oa',
        'nama_oa',
        'kode_gudang',
        'kontrak_oa',
        'muatan_oa',
        'harga_oa',
        'harga_kso',
        'harga_kso_khusus',
        'wilayah_oa',
        'keterangan',
    ];

    protected $casts = [
        'harga_oa' => 'decimal:2',
        'harga_kso' => 'decimal:2',
        'harga_kso_khusus' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'harga_oa_rupiah',
        'harga_kso_rupiah',
        'harga_kso_khusus_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Relasi ke data fasilitas Gudang Asal
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    /**
     * Accessor harga OA format Rupiah
     */
    public function getHargaOaRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_oa ?? 0, 0, ',', '.');
    }

    /**
     * Accessor harga KSO format Rupiah
     */
    public function getHargaKsoRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_kso ?? 0, 0, ',', '.');
    }

    /**
     * Accessor harga KSO Khusus format Rupiah
     */
    public function getHargaKsoKhususRupiahAttribute()
    {
        return 'Rp ' . number_format($this->harga_kso_khusus ?? 0, 0, ',', '.');
    }

    /**
     * Accessor riwayat diedit relatif waktu
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return 'Baru didaftarkan';
        }
        return $waktu->locale('id')->diffForHumans();
    }

    /**
     * Accessor tanggal jam riwayat diedit presisi
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
