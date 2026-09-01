<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Wilayah;

class OngkosAngkut extends Model
{
    use HasFactory;

    protected $table = 'data_ongkos_angkut';
    protected $primaryKey = 'id_ongkos';

    public $timestamps = false;

    protected $fillable = [
        'kode_wilayah_asal',
        'kode_wilayah_tujuan',
        'tarif_per_zak',
        'tarif_per_ritase',
        'keterangan',
    ];

    public function wilayahAsal()
    {
        return $this->belongsTo(Wilayah::class, 'kode_wilayah_asal', 'kode_wilayah');
    }

    public function wilayahTujuan()
    {
        return $this->belongsTo(Wilayah::class, 'kode_wilayah_tujuan', 'kode_wilayah');
    }
}
