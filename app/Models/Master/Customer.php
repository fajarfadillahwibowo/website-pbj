<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'data_customer';
    protected $primaryKey = 'kode_customer';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_customer',
        'kode_wilayah',
        'nama_toko_bangunan',
        'nama_pemilik',
        'alamat',
        'no_hp',
        'no_ktp',
        'foto_ktp',
        'plafon_piutang',
        'saldo_piutang',
        'saldo_deposit',
    ];

    protected $appends = [
        'nama_customer',
    ];

    /**
     * Accessor alias nama_customer ke nama_toko_bangunan
     */
    public function getNamaCustomerAttribute()
    {
        return $this->nama_toko_bangunan ?: ($this->nama_pemilik ?: $this->kode_customer);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'kode_wilayah', 'kode_wilayah');
    }
}
