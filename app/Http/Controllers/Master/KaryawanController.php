<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Karyawan;
use App\Models\Autentikasi\Jabatan;
use App\Helpers\GeneratorKodeOtomatis;

class KaryawanController extends Controller
{
    /**
     * Tampilkan seluruh master data karyawan (Selaras dengan Data Karyawan Driver).
     */
    public function index(Request $request)
    {
        return redirect()->route('operasional.armada.driver', $request->all());
    }

    /**
     * Simpan data karyawan baru.
     */
    public function store(Request $request)
    {
        // Isi kode otomatis jika kosong
        if (!$request->filled('kode_karyawan')) {
            $request->merge([
                'kode_karyawan' => GeneratorKodeOtomatis::buatKode('data_karyawan', 'kode_karyawan', 'KRY-', 3)
            ]);
        }

        $request->validate([
            'kode_karyawan'     => 'required|string|max:30|unique:data_karyawan,kode_karyawan',
            'nama_karyawan'     => 'required|string|max:100',
            'id_jabatan'        => 'required|integer|exists:jabatan,id_jabatan',
            'kategori_karyawan' => 'required|string|in:staf,driver,teknisi,gudang,manajemen',
            'no_identitas'      => 'required|numeric|digits:16',
            'no_hp'             => 'required|string|max:25',
            'alamat'            => 'required|string',
            'status_karyawan'   => 'required|string|in:aktif,kontrak,tetap,non-aktif,berhenti',
        ], [
            'kode_karyawan.unique'  => 'Kode karyawan sudah digunakan.',
            'id_jabatan.exists'     => 'Jabatan yang dipilih tidak valid.',
            'no_identitas.required' => 'NIK (Nomor Induk Kependudukan) wajib diisi.',
            'no_identitas.digits'   => 'NIK wajib terdiri dari tepat 16 digit angka numerik resmi.',
            'no_identitas.numeric'  => 'NIK hanya boleh berisi karakter angka (0-9).',
        ]);

        Karyawan::create([
            'kode_karyawan'       => strtoupper($request->kode_karyawan),
            'nama_karyawan'       => $request->nama_karyawan,
            'id_jabatan'          => $request->id_jabatan,
            'kategori_karyawan'   => $request->kategori_karyawan,
            'no_identitas'        => trim($request->no_identitas),
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
            'no_identitas'      => 'required|numeric|digits:16',
            'no_hp'             => 'required|string|max:25',
            'alamat'            => 'required|string',
            'status_karyawan'   => 'required|string|in:aktif,kontrak,tetap,non-aktif,berhenti',
        ], [
            'id_jabatan.exists'     => 'Jabatan yang dipilih tidak valid.',
            'no_identitas.required' => 'NIK (Nomor Induk Kependudukan) wajib diisi.',
            'no_identitas.digits'   => 'NIK wajib terdiri dari tepat 16 digit angka numerik resmi.',
            'no_identitas.numeric'  => 'NIK hanya boleh berisi karakter angka (0-9).',
        ]);

        $karyawan->update([
            'nama_karyawan'     => $request->nama_karyawan,
            'id_jabatan'        => $request->id_jabatan,
            'kategori_karyawan' => $request->kategori_karyawan,
            'no_identitas'      => trim($request->no_identitas),
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
