<?php

namespace App\Http\Controllers\Keuangan\AR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepositCustomerController extends Controller
{
    public function index()
    {
        return view('keuangan.ar.deposit_customer');
    }
}
