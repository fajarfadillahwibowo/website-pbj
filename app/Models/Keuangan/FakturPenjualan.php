<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;

class FakturPenjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_faktur',
        'tanggal_penjualan',
        'kode_customer',
        'metode_pembayaran',
        'total_bruto',
        'diskon',
        'total_netto',
        'jumlah_dibayar',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'id_rekening',
        'status_persetujuan',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'jatuh_tempo' => 'date',
        'total_bruto' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_netto' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'total_netto_rupiah',
        'sisa_piutang_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getTotalNettoRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_netto ?? 0, 0, ',', '.');
    }

    public function getSisaPiutangRupiahAttribute()
    {
        return 'Rp ' . number_format($this->sisa_piutang ?? 0, 0, ',', '.');
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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_penjualan', 'id_penjualan');
    }
}
