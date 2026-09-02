<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    use HasFactory;

    protected $table = 'data_kode_akun';
    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'kelompok_akun',
        'saldo_normal',
        'saldo_awal',
        'saldo_berjalan',
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_berjalan' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'saldo',
        'saldo_rupiah',
        'saldo_berjalan_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getSaldoAttribute()
    {
        return $this->saldo_berjalan ?? 0;
    }

    public function getSaldoRupiahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_berjalan ?? 0, 0, ',', '.');
    }

    public function getSaldoBerjalanRupiahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_berjalan ?? 0, 0, ',', '.');
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

    public function daftarJurnal()
    {
        return $this->hasMany(JurnalUmum::class, 'kode_akun', 'kode_akun');
    }
}
