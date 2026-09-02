<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'data_customer';
    protected $primaryKey = 'kode_customer';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_customer',
        'kode_wilayah',
        'nama_toko_bangunan',
        'nama_pemilik',
        'alamat',
        'no_hp',
        'no_ktp',
        'foto_ktp',
        'plafon_piutang',
        'saldo_piutang',
        'saldo_deposit',
    ];

    protected $casts = [
        'plafon_piutang' => 'decimal:2',
        'saldo_piutang' => 'decimal:2',
        'saldo_deposit' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'nama_customer',
        'plafon_piutang_rupiah',
        'saldo_piutang_rupiah',
        'saldo_deposit_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    /**
     * Accessor alias nama_customer ke nama_toko_bangunan
     */
    public function getNamaCustomerAttribute()
    {
        return $this->nama_toko_bangunan ?: ($this->nama_pemilik ?: $this->kode_customer);
    }

    public function getPlafonPiutangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->plafon_piutang ?? 0, 0, ',', '.');
    }

    public function getSaldoPiutangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_piutang ?? 0, 0, ',', '.');
    }

    public function getSaldoDepositRupiahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_deposit ?? 0, 0, ',', '.');
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

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'kode_wilayah', 'kode_wilayah');
    }
}
