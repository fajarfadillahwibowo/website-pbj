<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PiutangController extends Controller
{
    public function index()
    {
        return view('keuangan.ar.list_piutang');
    }
}
