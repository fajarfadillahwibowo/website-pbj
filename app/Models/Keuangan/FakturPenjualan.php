<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturPenjualan extends Model
{
    use HasFactory;

    protected $table = 'faktur_penjualan';

    protected $fillable = [
        'nomor_faktur',
        'customer_id',
        'tanggal_faktur',
        'total_tagihan',
        'status_pembayaran',
    ];
}
