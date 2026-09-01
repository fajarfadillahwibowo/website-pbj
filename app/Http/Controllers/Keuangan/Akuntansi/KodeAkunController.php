<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KodeAkunController extends Controller
{
    public function index()
    {
        return view('keuangan.akuntansi.kode_akun');
    }
}
