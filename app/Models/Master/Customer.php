<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = [
        'nama_toko',
        'nama_pemilik',
        'no_telepon',
        'alamat_lengkap',
        'wilayah_id',
        'limit_piutang',
        'saldo_deposit',
    ];
}
