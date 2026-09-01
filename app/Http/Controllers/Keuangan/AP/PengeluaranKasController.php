<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengeluaranKasController extends Controller
{
    public function index()
    {
        return view('keuangan.ap.pengeluaran_kas');
    }
}
