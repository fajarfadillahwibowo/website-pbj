<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Wilayah;
use App\Helpers\GeneratorKodeOtomatis;

class WilayahController extends Controller
{
    /**
     * Tampilkan daftar master wilayah zonasi distribusi semen.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');

        $query = Wilayah::withCount('daftarCustomer');

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_wilayah', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_wilayah', 'like', "%{$kataKunci}%");
            });
        }

        $daftarWilayah = $query->orderBy('kode_wilayah', 'asc')->get();
        $totalWilayah = Wilayah::count();

        // Generator kode wilayah otomatis
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_wilayah', 'kode_wilayah', 'WLY-', 3);

        return view('master.wilayah.index', compact(
            'daftarWilayah',
            'kataKunci',
            'totalWilayah',
            'kodeOtomatis'
        ));
    }

    /**
     * Simpan data wilayah zonasi baru.
     */
    public function store(Request $request)
    {
        // Isi kode otomatis jika kosong
        if (!$request->filled('kode_wilayah')) {
            $request->merge([
                'kode_wilayah' => GeneratorKodeOtomatis::buatKode('data_wilayah', 'kode_wilayah', 'WLY-', 3)
            ]);
        }

        $request->validate([
            'kode_wilayah' => 'required|string|max:30|unique:data_wilayah,kode_wilayah',
            'nama_wilayah' => 'required|string|max:100',
        ], [
            'kode_wilayah.unique' => 'Kode wilayah sudah digunakan.',
        ]);

        Wilayah::create([
            'kode_wilayah' => strtoupper($request->kode_wilayah),
            'nama_wilayah' => $request->nama_wilayah,
        ]);

        return redirect()->route('master.wilayah.index')->with('sukses', "Wilayah '{$request->nama_wilayah}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data wilayah zonasi.
     */
    public function update(Request $request, $kode_wilayah)
    {
        $wilayah = Wilayah::findOrFail($kode_wilayah);

        $request->validate([
            'nama_wilayah' => 'required|string|max:100',
        ]);

        $wilayah->update([
            'nama_wilayah' => $request->nama_wilayah,
        ]);

        return redirect()->route('master.wilayah.index')->with('sukses', "Wilayah '{$wilayah->nama_wilayah}' berhasil diperbarui.");
    }

    /**
     * Hapus data wilayah jika belum ada customer terhubung.
     */
    public function destroy($kode_wilayah)
    {
        $wilayah = Wilayah::withCount('daftarCustomer')->findOrFail($kode_wilayah);

        if ($wilayah->daftar_customer_count > 0) {
            return redirect()->route('master.wilayah.index')->with('gagal', "Wilayah '{$wilayah->nama_wilayah}' tidak dapat dihapus karena memiliki {$wilayah->daftar_customer_count} data customer toko yang terhubung.");
        }

        $wilayah->delete();

        return redirect()->route('master.wilayah.index')->with('sukses', "Wilayah '{$wilayah->nama_wilayah}' berhasil dihapus.");
    }

    /**
     * Generator Kode Wilayah Otomatis (Daur Ulang Slot vs Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return GeneratorKodeOtomatis::responJson('data_wilayah', 'kode_wilayah', 'WLY-', $mode, 3);
    }
}
