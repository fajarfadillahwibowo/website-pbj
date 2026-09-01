<?php

namespace App\Http\Controllers\Operasional\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        return view('operasional.gudang.opname');
    }
}
