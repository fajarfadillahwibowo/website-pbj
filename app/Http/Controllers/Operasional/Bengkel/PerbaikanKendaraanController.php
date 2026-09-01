<?php

namespace App\Http\Controllers\Operasional\Bengkel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerbaikanKendaraanController extends Controller
{
    public function index()
    {
        return view('operasional.bengkel.perbaikan');
    }
}
