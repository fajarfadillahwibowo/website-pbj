<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'nomor_plat',
        'tipe_truk', // Tronton / Colt Diesel
        'kapasitas_tonase',
        'status_operasional',
    ];
}
