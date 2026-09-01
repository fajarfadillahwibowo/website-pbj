<?php

namespace App\Http\Controllers\Operasional\Bengkel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembelianSparepartController extends Controller
{
    /**
     * Tampilkan riwayat dan formulir pembelian sparepart bengkel armada.
     */
    public function index()
    {
        return view('operasional.bengkel.pembelian_sparepart');
    }
}
