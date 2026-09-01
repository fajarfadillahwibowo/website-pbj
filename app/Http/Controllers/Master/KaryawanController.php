<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Karyawan;
use App\Models\Autentikasi\Jabatan;

class KaryawanController extends Controller
{
    /**
     * Tampilkan seluruh master data karyawan (Staf, Driver, Gudang, Teknisi, Manajemen).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterKategori = $request->input('kategori');

        $query = Karyawan::with('jabatan');

        if ($filterKategori && in_array($filterKategori, ['staf', 'driver', 'teknisi', 'gudang', 'manajemen'])) {
            $query->where('kategori_karyawan', $filterKategori);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp', 'like', "%{$kataKunci}%")
                  ->orWhere('no_identitas', 'like', "%{$kataKunci}%");
            });
        }

        $daftarKaryawan = $query->orderBy('kode_karyawan', 'asc')->get();
        $daftarJabatan = Jabatan::orderBy('id_jabatan', 'asc')->get();

        // Hitung statistik per kategori
        $totalSemua = Karyawan::count();
        $totalDriver = Karyawan::where('kategori_karyawan', 'driver')->count();
        $totalStaf = Karyawan::where('kategori_karyawan', 'staf')->count();
        $totalGudang = Karyawan::where('kategori_karyawan', 'gudang')->count();
        $totalTeknisi = Karyawan::where('kategori_karyawan', 'teknisi')->count();

        return view('master.karyawan.index', compact(
            'daftarKaryawan',
            'daftarJabatan',
            'kataKunci',
            'filterKategori',
            'totalSemua',
            'totalDriver',
            'totalStaf',
            'totalGudang',
            'totalTeknisi'
        ));
    }
}
