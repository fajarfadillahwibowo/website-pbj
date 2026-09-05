<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $query = Wilayah::withCount(['daftarCustomer', 'daftarToko']);

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
     * Hapus data wilayah jika belum ada customer atau toko terhubung.
     */
    public function destroy($kode_wilayah)
    {
        $wilayah = Wilayah::withCount(['daftarCustomer', 'daftarToko'])->findOrFail($kode_wilayah);

        if ($wilayah->daftar_customer_count > 0 || $wilayah->daftar_toko_count > 0) {
            $detailKeterikatan = [];
            if ($wilayah->daftar_customer_count > 0) {
                $detailKeterikatan[] = "{$wilayah->daftar_customer_count} customer pemilik";
            }
            if ($wilayah->daftar_toko_count > 0) {
                $detailKeterikatan[] = "{$wilayah->daftar_toko_count} toko cabang / proyek";
            }
            $teksKeterikatan = implode(' dan ', $detailKeterikatan);

            return redirect()->route('master.wilayah.index')->with('gagal', "Wilayah '{$wilayah->nama_wilayah}' tidak dapat dihapus karena masih terhubung dengan {$teksKeterikatan}.");
        }

        $wilayah->delete();

        return redirect()->route('master.wilayah.index')->with('sukses', "Wilayah '{$wilayah->nama_wilayah}' berhasil dihapus.");
    }

    /**
     * Hapus banyak data wilayah sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('master.wilayah.index')->with('gagal', 'Tidak ada data wilayah yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;
        $gagalDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $wilayah = Wilayah::withCount(['daftarCustomer', 'daftarToko'])->find($kode);
                if ($wilayah) {
                    if ($wilayah->daftar_customer_count > 0 || $wilayah->daftar_toko_count > 0) {
                        $gagalDihapus++;
                        continue;
                    }
                    $wilayah->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            if ($gagalDihapus > 0) {
                return redirect()->route('master.wilayah.index')->with('sukses', "{$berhasilDihapus} wilayah berhasil dihapus. {$gagalDihapus} wilayah dilewati karena masih memiliki customer pemilik atau toko cabang terhubung.");
            }

            return redirect()->route('master.wilayah.index')->with('sukses', "{$berhasilDihapus} data wilayah terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.wilayah.index')->with('gagal', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }
}
