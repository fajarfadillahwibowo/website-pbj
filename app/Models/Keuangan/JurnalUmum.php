<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'dibuat_pada'       => 'datetime',
        'diperbarui_pada'   => 'datetime',
    ];

    /**
     * Accessor riwayat diedit / dibuat relatif waktu (contoh: '3 hari yang lalu')
     */
    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return 'Baru dicatat';
        }
        return \Carbon\Carbon::parse($waktu)->locale('id')->diffForHumans();
    }

    /**
     * Accessor tanggal jam riwayat diedit / dibuat presisi
     */
    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) {
            return '-';
        }
        return \Carbon\Carbon::parse($waktu)->format('d/m/Y H:i:s');
    }

    protected $fillable = [
        'nomor_jurnal',
        'tanggal_transaksi',
        'kode_akun',
        'posisi',
        'nominal',
        'keterangan',
        'referensi_transaksi',
        'dibuat_oleh',
    ];

    public function akun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun', 'kode_akun');
    }
}
