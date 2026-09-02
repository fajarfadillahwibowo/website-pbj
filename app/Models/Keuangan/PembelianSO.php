<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;
use App\Models\Operasional\Gudang;

class PembelianSO extends Model
{
    use HasFactory;

    protected $table = 'pembelian_so';
    protected $primaryKey = 'id_so';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'nomor_so',
        'tanggal_so',
        'kode_customer',
        'kode_gudang',
        'jumlah_zak',
        'harga_satuan',
        'total_harga',
        'status_so',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_so' => 'date',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'dibuat_pada' => 'datetime',
        'diperbarui_pada' => 'datetime',
    ];

    protected $appends = [
        'total_harga_rupiah',
        'terakhir_diedit_relatif',
        'terakhir_diedit_waktu',
    ];

    public function getTotalHargaRupiahAttribute()
    {
        return 'Rp ' . number_format($this->total_harga ?? 0, 0, ',', '.');
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

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
