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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }
}
