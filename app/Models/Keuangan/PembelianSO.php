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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }
}
