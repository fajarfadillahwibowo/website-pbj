<?php

namespace App\Http\Controllers\Operasional\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MonitoringOperasionalController extends Controller
{
    public function index()
    {
        return view('operasional.monitoring.index');
    }
}
