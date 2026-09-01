<?php

namespace App\Http\Controllers\Operasional\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenerimaanBarangController extends Controller
{
    public function index()
    {
        return view('operasional.gudang.penerimaan_barang');
    }
}
