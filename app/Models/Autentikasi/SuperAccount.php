<?php

namespace App\Models\Autentikasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAccount extends Authenticatable
{
    use HasFactory;

    protected $table = 'super_account';
    protected $primaryKey = 'id_super_account';

    const CREATED_AT = 'tanggal_create';
    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'username',
        'password',
        'nama_pemilik',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
