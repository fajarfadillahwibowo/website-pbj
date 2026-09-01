<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';

    protected $fillable = [
        'nomor_surat_jalan',
        'sales_order_id',
        'kendaraan_id',
        'driver_id',
        'tanggal_pengiriman',
        'status_pengiriman',
    ];
}
