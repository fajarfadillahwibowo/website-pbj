<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanEksekutifController extends Controller
{
    public function neraca()
    {
        return view('laporan.neraca');
    }

    public function labaRugi()
    {
        return view('laporan.laba_rugi');
    }

    public function arusKas()
    {
        return view('laporan.arus_kas');
    }
}
