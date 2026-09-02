<?php

namespace App\Http\Controllers\Keuangan\AP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan\PembelianSO;
use App\Models\Operasional\Gudang;

class ListSOController extends Controller
{
    /**
     * Tampilkan tabel monitoring kuota pengambilan semen (List SO).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');
        $filterGudang = $request->input('gudang');

        $query = PembelianSO::with(['customer', 'gudang']);

        if ($filterStatus) {
            $query->where('status_so', $filterStatus);
        }

        if ($filterGudang) {
            $query->where('kode_gudang', $filterGudang);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_so', 'like', "%{$kataKunci}%")
                  ->orWhere('nomor_lo', 'like', "%{$kataKunci}%")
                  ->orWhereHas('gudang', function ($g) use ($kataKunci) {
                      $g->where('nama_gudang', 'like', "%{$kataKunci}%")
                        ->orWhere('plant', 'like', "%{$kataKunci}%");
                  });
            });
        }

        $daftarSO = $query->orderBy('id_so', 'desc')->get();
        $daftarGudang = Gudang::orderBy('nama_gudang')->get();

        // Agregat Statistik Kuota
        $totalSO = PembelianSO::count();
        $totalSOAktif = PembelianSO::whereIn('status_so', ['disetujui', 'diproses', 'dikirim', 'aktif'])->count();
        $totalKuantitasSO = PembelianSO::sum('jumlah_zak');
        $totalTerambil = PembelianSO::sum('qty_pengambilan');
        $totalSisaKuota = max(0, $totalKuantitasSO - $totalTerambil);

        return view('keuangan.ap.list_so', compact(
            'daftarSO',
            'daftarGudang',
            'kataKunci',
            'filterStatus',
            'filterGudang',
            'totalSO',
            'totalSOAktif',
            'totalKuantitasSO',
            'totalTerambil',
            'totalSisaKuota'
        ));
    }
}
