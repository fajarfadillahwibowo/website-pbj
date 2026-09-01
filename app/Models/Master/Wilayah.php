<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'data_wilayah';
    protected $primaryKey = 'kode_wilayah';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_wilayah',
        'nama_wilayah',
    ];

    public function daftarCustomer()
    {
        return $this->hasMany(Customer::class, 'kode_wilayah', 'kode_wilayah');
    }
}
