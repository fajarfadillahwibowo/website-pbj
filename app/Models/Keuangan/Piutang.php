<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Customer;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'list_piutang';
    protected $primaryKey = 'id_piutang';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'no_faktur',
        'kode_customer',
        'total_piutang',
        'sisa_piutang',
        'tanggal_jatuh_tempo',
        'status_piutang',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }

    public function faktur()
    {
        return $this->belongsTo(FakturPenjualan::class, 'no_faktur', 'no_faktur');
    }
}
