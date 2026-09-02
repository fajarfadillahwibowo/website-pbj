<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;

class DepositCustomer extends Model
{
    use HasFactory;

    protected $table = 'list_deposit';
    protected $primaryKey = 'id_deposit';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'nomor_bukti_deposit',
        'kode_customer',
        'tanggal_deposit',
        'tipe_mutasi',
        'jumlah_nominal',
        'saldo_akhir_deposit',
        'keterangan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_deposit' => 'date',
        'jumlah_nominal' => 'decimal:2',
        'saldo_akhir_deposit' => 'decimal:2',
        'dibuat_pada' => 'datetime',
    ];

    protected $appends = [
        'jumlah_nominal_rupiah',
        'saldo_akhir_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getJumlahNominalRupiahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah_nominal ?? 0, 0, ',', '.');
    }

    public function getSaldoAkhirRupiahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_akhir_deposit ?? 0, 0, ',', '.');
    }

    public function getTerakhirDieditRelatifAttribute()
    {
        $waktu = $this->dibuat_pada;
        if (!$waktu) return 'Baru dibuat';
        return $waktu->locale('id')->diffForHumans();
    }

    public function getTerakhirDieditWaktuAttribute()
    {
        $waktu = $this->dibuat_pada;
        if (!$waktu) return '-';
        return $waktu->format('d/m/Y H:i:s');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }
}
