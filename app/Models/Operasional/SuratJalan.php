<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Karyawan;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'no_surat_jalan';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'no_surat_jalan',
        'tanggal_kirim',
        'kode_karyawan_driver',
        'plat_nomor',
        'tujuan_alamat',
        'jumlah_zak',
        'status_pengiriman',
        'keterangan',
    ];

    public function driver()
    {
        return $this->belongsTo(Karyawan::class, 'kode_karyawan_driver', 'kode_karyawan');
    }
}
