<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Barang;
use App\Helpers\GeneratorKodeOtomatis;

class BarangController extends Controller
{
    /**
     * Tampilkan daftar master produk semen (Zak & Curah).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterJenis = $request->input('jenis');

        $query = Barang::query();

        if ($filterJenis) {
            $query->where('jenis_barang', $filterJenis);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_barang', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_barang', 'like', "%{$kataKunci}%");
            });
        }

        $daftarBarang = $query->orderBy('kode_barang', 'asc')->get();

        $totalBarang = Barang::count();
        $totalZak = Barang::where('jenis_barang', 'Zak')->count();
        $totalCurah = Barang::where('jenis_barang', 'Curah')->count();

        // Generator kode produk otomatis
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_semen', 'kode_barang', 'SMN-', 3);

        return view('master.barang.index', compact(
            'daftarBarang',
            'kataKunci',
            'filterJenis',
            'totalBarang',
            'totalZak',
            'totalCurah',
            'kodeOtomatis'
        ));
    }

    /**
     * Simpan produk semen baru.
     */
    public function store(Request $request)
    {
        // Isi kode otomatis jika kosong
        if (!$request->filled('kode_barang')) {
            $request->merge([
                'kode_barang' => GeneratorKodeOtomatis::buatKode('data_semen', 'kode_barang', 'SMN-', 3)
            ]);
        }

        $request->validate([
            'kode_barang'        => 'required|string|max:30|unique:data_semen,kode_barang',
            'nama_barang'        => 'required|string|max:150',
            'jenis_barang'       => 'required|string|in:Zak,Curah',
            'satuan_barang'      => 'required|string|max:20',
            'harga_pokok'        => 'required|numeric|min:0',
            'harga_jual_standar' => 'required|numeric|min:0',
        ], [
            'kode_barang.unique' => 'Kode produk semen sudah digunakan.',
            'jenis_barang.in'    => 'Jenis barang harus Zak atau Curah.',
        ]);

        Barang::create([
            'kode_barang'        => strtoupper($request->kode_barang),
            'nama_barang'        => $request->nama_barang,
            'jenis_barang'       => $request->jenis_barang,
            'satuan_barang'      => $request->satuan_barang,
            'harga_pokok'        => $request->harga_pokok,
            'harga_jual_standar' => $request->harga_jual_standar,
        ]);

        return redirect()->route('master.barang.index')->with('sukses', "Produk '{$request->nama_barang}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data produk semen.
     */
    public function update(Request $request, $kode_barang)
    {
        $barang = Barang::findOrFail($kode_barang);

        $request->validate([
            'nama_barang'        => 'required|string|max:150',
            'jenis_barang'       => 'required|string|in:Zak,Curah',
            'satuan_barang'      => 'required|string|max:20',
            'harga_pokok'        => 'required|numeric|min:0',
            'harga_jual_standar' => 'required|numeric|min:0',
        ]);

        $barang->update([
            'nama_barang'        => $request->nama_barang,
            'jenis_barang'       => $request->jenis_barang,
            'satuan_barang'      => $request->satuan_barang,
            'harga_pokok'        => $request->harga_pokok,
            'harga_jual_standar' => $request->harga_jual_standar,
        ]);

        return redirect()->route('master.barang.index')->with('sukses', "Produk '{$barang->nama_barang}' berhasil diperbarui.");
    }

    /**
     * Hapus produk semen.
     */
    public function destroy($kode_barang)
    {
        $barang = Barang::findOrFail($kode_barang);
        $barang->delete();

        return redirect()->route('master.barang.index')->with('sukses', "Produk '{$barang->nama_barang}' berhasil dihapus.");
    }

    /**
     * Hapus banyak produk semen sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('master.barang.index')->with('gagal', 'Tidak ada produk semen yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $barang = Barang::find($kode);
                if ($barang) {
                    $barang->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            return redirect()->route('master.barang.index')->with('sukses', "{$berhasilDihapus} produk semen terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.barang.index')->with('gagal', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }
}
