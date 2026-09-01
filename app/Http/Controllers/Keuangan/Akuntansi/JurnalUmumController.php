<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JurnalUmumController extends Controller
{
    public function index()
    {
        return view('keuangan.akuntansi.jurnal_umum');
    }
}
