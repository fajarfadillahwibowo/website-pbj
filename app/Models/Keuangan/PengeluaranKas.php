<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranKas extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_pengeluaran',
        'tanggal_pengeluaran',
        'kategori_pengeluaran',
        'kode_akun',
        'total_nominal',
        'id_rekening_sumber',
        'keterangan',
        'status_persetujuan',
        'disetujui_oleh',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'total_nominal' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'total_nominal_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getTotalNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_nominal ?? 0, 0, ',', '.');
    }

    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return 'Baru dibuat';
        return $waktu->locale('id')->diffForHumans();
    }

    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->diperbarui_pada ?? $this->dibuat_pada;
        if (!$waktu) return '-';
        return $waktu->format('d/m/Y H:i:s');
    }

    public function akun()
    {
        return $this->belongsTo(KodeAkun::class, 'kode_akun', 'kode_akun');
    }
}
