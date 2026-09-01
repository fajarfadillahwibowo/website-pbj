<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KSO extends Model
{
    use HasFactory;

    protected $table = 'data_kso';
    protected $primaryKey = 'kode_kso';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'kode_kso',
        'nama_kso',
        'pihak_mitra',
        'tanggal_mulai',
        'tanggal_selesai',
        'nilai_kontrak',
        'keterangan',
    ];
}
