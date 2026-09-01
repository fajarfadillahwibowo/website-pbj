<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'data_wilayah';
    protected $primaryKey = 'id_wilayah';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = null;

    protected $fillable = [
        'kode_wilayah',
        'nama_wilayah',
        'provinsi',
        'kota_kabupaten',
    ];

    public function daftarCustomer()
    {
        return $this->hasMany(Customer::class, 'id_wilayah', 'id_wilayah');
    }
}
