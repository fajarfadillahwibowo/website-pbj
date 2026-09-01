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
        'kode_customer',
        'tanggal_transaksi',
        'jenis_mutasi',
        'nominal',
        'saldo_akhir',
        'keterangan',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }
}
