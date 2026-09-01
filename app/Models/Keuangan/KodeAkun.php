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
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'tipe_akun',
        'kelompok_akun',
        'saldo_normal',
        'saldo_awal',
        'saldo_berjalan',
    ];

    public function daftarJurnal()
    {
        return $this->hasMany(JurnalUmum::class, 'kode_akun', 'kode_akun');
    }
}
