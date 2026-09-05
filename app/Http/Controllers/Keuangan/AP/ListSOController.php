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

        $daftarSO = $query->with('daftarPengiriman')->orderBy('id_so', 'desc')->get();

        // Sinkronisasi otomatis kuota pengambilan aktual dari pengiriman disetujui
        foreach ($daftarSO as $so) {
            $totalPengirimanTerambil = $so->daftarPengiriman
                ->whereIn('status_pengiriman', ['dalam_perjalanan', 'terkirim'])
                ->sum('jumlah_zak');

            if ($so->qty_pengambilan != $totalPengirimanTerambil) {
                $so->qty_pengambilan = $totalPengirimanTerambil;
                $statusOtomatis = ($totalPengirimanTerambil >= $so->jumlah_zak && $so->jumlah_zak > 0)
                    ? 'selesai'
                    : ($totalPengirimanTerambil > 0 ? 'dikirim' : $so->status_so);
                
                \Illuminate\Support\Facades\DB::table('pembelian_so')
                    ->where('id_so', $so->id_so)
                    ->update([
                        'qty_pengambilan' => $totalPengirimanTerambil,
                        'status_so'       => $statusOtomatis,
                        'diperbarui_pada' => now(),
                    ]);
                $so->status_so = $statusOtomatis;
            }
        }

        $daftarGudang = Gudang::orderBy('nama_gudang')->get();

        // Agregat Statistik Kuota Real-Time
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
