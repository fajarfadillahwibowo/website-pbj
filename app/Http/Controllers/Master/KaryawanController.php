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

    /**
     * Simpan data karyawan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_karyawan'     => 'required|string|max:30|unique:data_karyawan,kode_karyawan',
            'nama_karyawan'     => 'required|string|max:100',
            'id_jabatan'        => 'required|integer|exists:jabatan,id_jabatan',
            'kategori_karyawan' => 'required|string|in:staf,driver,teknisi,gudang,manajemen',
            'no_identitas'      => 'required|string|max:30',
            'no_hp'             => 'required|string|max:25',
            'alamat'            => 'required|string',
            'status_karyawan'   => 'required|string|in:aktif,kontrak,tetap,non-aktif,berhenti',
        ], [
            'kode_karyawan.unique' => 'Kode karyawan sudah digunakan.',
            'id_jabatan.exists'    => 'Jabatan yang dipilih tidak valid.',
        ]);

        Karyawan::create([
            'kode_karyawan'       => strtoupper($request->kode_karyawan),
            'nama_karyawan'       => $request->nama_karyawan,
            'id_jabatan'          => $request->id_jabatan,
            'kategori_karyawan'   => $request->kategori_karyawan,
            'no_identitas'        => $request->no_identitas,
            'no_hp'               => $request->no_hp,
            'alamat'              => $request->alamat,
            'status_karyawan'     => $request->status_karyawan,
            'tanggal_mulai_kerja' => $request->tanggal_mulai_kerja ?? date('Y-m-d'),
        ]);

        return redirect()->route('master.karyawan.index')->with('sukses', "Karyawan '{$request->nama_karyawan}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data karyawan.
     */
    public function update(Request $request, $kode_karyawan)
    {
        $karyawan = Karyawan::findOrFail($kode_karyawan);

        $request->validate([
            'nama_karyawan'     => 'required|string|max:100',
            'id_jabatan'        => 'required|integer|exists:jabatan,id_jabatan',
            'kategori_karyawan' => 'required|string|in:staf,driver,teknisi,gudang,manajemen',
            'no_identitas'      => 'required|string|max:30',
            'no_hp'             => 'required|string|max:25',
            'alamat'            => 'required|string',
            'status_karyawan'   => 'required|string|in:aktif,kontrak,tetap,non-aktif,berhenti',
        ]);

        $karyawan->update([
            'nama_karyawan'     => $request->nama_karyawan,
            'id_jabatan'        => $request->id_jabatan,
            'kategori_karyawan' => $request->kategori_karyawan,
            'no_identitas'      => $request->no_identitas,
            'no_hp'             => $request->no_hp,
            'alamat'            => $request->alamat,
            'status_karyawan'   => $request->status_karyawan,
        ]);

        return redirect()->route('master.karyawan.index')->with('sukses', "Data Karyawan '{$karyawan->nama_karyawan}' berhasil diperbarui.");
    }

    /**
     * Hapus data karyawan.
     */
    public function destroy($kode_karyawan)
    {
        $karyawan = Karyawan::findOrFail($kode_karyawan);
        $karyawan->delete();

        return redirect()->route('master.karyawan.index')->with('sukses', "Karyawan '{$karyawan->nama_karyawan}' berhasil dihapus.");
    }
}
