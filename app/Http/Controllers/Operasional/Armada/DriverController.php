<?php

namespace App\Http\Controllers\Operasional\Armada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Operasional\Driver;

class DriverController extends Controller
{
    /**
     * Tampilkan data khusus karyawan dengan kategori Driver Supir.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');

        $query = Driver::with('jabatan');

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp', 'like', "%{$kataKunci}%")
                  ->orWhere('no_identitas', 'like', "%{$kataKunci}%");
            });
        }

        $daftarDriver = $query->orderBy('nama_karyawan', 'asc')->get();

        return view('operasional.armada.driver', compact('daftarDriver', 'kataKunci'));
    }
}
