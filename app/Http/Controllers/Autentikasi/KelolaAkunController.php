<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KelolaAkunController extends Controller
{
    /**
     * Tampilkan daftar akun staf dan hak akses pengguna (Super Admin).
     */
    public function index()
    {
        return view('superadmin.kelola_akun');
    }
}
