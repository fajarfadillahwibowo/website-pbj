<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeAkun extends Model
{
    use HasFactory;

    protected $table = 'data_kode_akun';
    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori_akun',
        'saldo_normal',
        'saldo_awal',
    ];

    public function jurnalUmum()
    {
        return $this->hasMany(JurnalUmum::class, 'kode_akun', 'kode_akun');
    }
}
