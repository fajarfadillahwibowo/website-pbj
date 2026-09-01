<?php

namespace App\Http\Controllers\Operasional\Pengiriman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        return view('operasional.pengiriman.sales_order');
    }
}
