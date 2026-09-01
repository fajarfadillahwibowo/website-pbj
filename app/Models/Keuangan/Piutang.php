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
        'id_penjualan',
        'kode_customer',
        'jumlah_piutang',
        'sisa_piutang',
        'tanggal_terbit',
        'tanggal_jatuh_tempo',
        'status_piutang',
    ];

    public function penjualan()
    {
        return $this->belongsTo(FakturPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'kode_customer', 'kode_customer');
    }
}
