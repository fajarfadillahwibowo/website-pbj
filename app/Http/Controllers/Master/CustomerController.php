<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Customer;
use App\Models\Master\Wilayah;
use App\Helpers\GeneratorKodeOtomatis;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar master customer (entitas pemilik finansial) beserta metrik & pencarian.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterWilayah = $request->input('wilayah');

        $query = Customer::with(['wilayah', 'tokoBangunan'])->withCount('tokoBangunan');

        if ($filterWilayah) {
            $query->where('kode_wilayah', $filterWilayah);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_pemilik', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_customer', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp', 'like', "%{$kataKunci}%")
                  ->orWhere('no_ktp', 'like', "%{$kataKunci}%");
            });
        }

        $daftarCustomer = $query->orderBy('kode_customer', 'asc')->get();
        $daftarWilayah = Wilayah::orderBy('nama_wilayah', 'asc')->get();

        $totalCustomer = Customer::count();
        $totalPlafon = Customer::sum('plafon_piutang');
        $totalPiutang = Customer::sum('saldo_piutang');
        $totalDeposit = Customer::sum('saldo_deposit');
        $totalTokoSemua = DB::table('data_toko_bangunan')->count();

        // Generator kode otomatis gap-filling
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_customer', 'kode_customer', 'CUST-', 3);

        $opsiWilayah = $daftarWilayah->map(fn($w) => [
            'nilai' => $w->kode_wilayah,
            'label' => $w->nama_wilayah,
            'sub'   => $w->kode_wilayah
        ])->toArray();

        return view('master.customer.index', compact(
            'daftarCustomer',
            'daftarWilayah',
            'kataKunci',
            'filterWilayah',
            'totalCustomer',
            'totalPlafon',
            'totalPiutang',
            'totalDeposit',
            'totalTokoSemua',
            'kodeOtomatis',
            'opsiWilayah'
        ));
    }

    /**
     * Simpan data customer baru.
     */
    public function store(Request $request)
    {
        // Isi kode otomatis jika kosong
        if (!$request->filled('kode_customer')) {
            $request->merge([
                'kode_customer' => GeneratorKodeOtomatis::buatKode('data_customer', 'kode_customer', 'CUST-', 3)
            ]);
        }

        $request->validate([
            'kode_customer'      => 'required|string|max:30|unique:data_customer,kode_customer',
            'kode_wilayah'       => 'required|string|exists:data_wilayah,kode_wilayah',
            'nama_pemilik'       => 'required|string|max:100',
            'nama_toko_bangunan' => 'required|string|max:150', // Nama Usaha / Badan
            'alamat'             => 'required|string',
            'no_hp'              => 'required|string|max:25',
            'plafon_piutang'     => 'required|numeric|min:0',
        ], [
            'kode_customer.unique'   => 'Kode customer sudah digunakan.',
            'kode_wilayah.exists'    => 'Wilayah yang dipilih tidak valid.',
            'plafon_piutang.numeric' => 'Plafon piutang harus berupa angka.',
        ]);

        Customer::create([
            'kode_customer'      => strtoupper($request->kode_customer),
            'kode_wilayah'       => $request->kode_wilayah,
            'nama_pemilik'       => $request->nama_pemilik,
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'alamat'             => $request->alamat,
            'no_hp'              => $request->no_hp,
            'no_ktp'             => $request->no_ktp ?? null,
            'plafon_piutang'     => $request->plafon_piutang,
            'saldo_piutang'      => 0.00,
            'saldo_deposit'      => $request->saldo_deposit ?? 0.00,
        ]);

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$request->nama_pemilik}' ({$request->nama_toko_bangunan}) berhasil ditambahkan.");
    }

    /**
     * Ambil detail kinerja 360 derajat data customer beserta daftar toko miliknya (JSON API).
     */
    public function ambilDetail($kode_customer)
    {
        $customer = Customer::with(['wilayah', 'tokoBangunan.wilayah'])->where('kode_customer', $kode_customer)->firstOrFail();

        // Hitung total transaksi penjualan
        $totalBelanja = DB::table('penjualan')->where('kode_customer', $kode_customer)->sum('total_netto') ?? 0;
        $totalTransaksi = DB::table('penjualan')->where('kode_customer', $kode_customer)->count();
        $sisaLimitKredit = max(0, $customer->plafon_piutang - $customer->saldo_piutang);

        $riwayatTransaksi = DB::table('penjualan')
            ->where('kode_customer', $kode_customer)
            ->orderBy('tanggal_penjualan', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'status'            => 'sukses',
            'customer'          => $customer,
            'toko_bangunan'     => $customer->tokoBangunan,
            'total_toko'        => $customer->tokoBangunan->count(),
            'total_belanja'     => (float)$totalBelanja,
            'total_transaksi'   => $totalTransaksi,
            'sisa_limit_kredit' => (float)$sisaLimitKredit,
            'riwayat_transaksi' => $riwayatTransaksi,
        ]);
    }

    /**
     * Perbarui data customer.
     */
    public function update(Request $request, $kode_customer)
    {
        $customer = Customer::findOrFail($kode_customer);

        $request->validate([
            'kode_wilayah'       => 'required|string|exists:data_wilayah,kode_wilayah',
            'nama_pemilik'       => 'required|string|max:100',
            'nama_toko_bangunan' => 'required|string|max:150',
            'alamat'             => 'required|string',
            'no_hp'              => 'required|string|max:25',
            'plafon_piutang'     => 'required|numeric|min:0',
        ]);

        $customer->update([
            'kode_wilayah'       => $request->kode_wilayah,
            'nama_pemilik'       => $request->nama_pemilik,
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'alamat'             => $request->alamat,
            'no_hp'              => $request->no_hp,
            'no_ktp'             => $request->no_ktp ?? $customer->no_ktp,
            'plafon_piutang'     => $request->plafon_piutang,
        ]);

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$customer->nama_pemilik}' berhasil diperbarui.");
    }

    /**
     * Hapus data customer jika tidak memiliki riwayat piutang aktif.
     */
    public function destroy($kode_customer)
    {
        $customer = Customer::findOrFail($kode_customer);

        if ($customer->saldo_piutang > 0) {
            return redirect()->route('master.customer.index')->with('gagal', "Customer '{$customer->nama_pemilik}' tidak dapat dihapus karena masih memiliki saldo piutang berjalan Rp " . number_format($customer->saldo_piutang, 0, ',', '.') . ".");
        }

        $customer->delete();

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$customer->nama_pemilik}' berhasil dihapus.");
    }

    /**
     * Hapus banyak data customer sekaligus (Hapus Massal).
     */
    public function hapusMassal(Request $request)
    {
        $daftarId = $request->input('daftar_id', []);
        if (empty($daftarId) || !is_array($daftarId)) {
            return redirect()->route('master.customer.index')->with('gagal', 'Tidak ada data customer yang dipilih untuk dihapus.');
        }

        $berhasilDihapus = 0;
        $gagalDihapus = 0;

        DB::beginTransaction();
        try {
            foreach ($daftarId as $kode) {
                $customer = Customer::find($kode);
                if ($customer) {
                    if ($customer->saldo_piutang > 0) {
                        $gagalDihapus++;
                        continue;
                    }
                    $customer->delete();
                    $berhasilDihapus++;
                }
            }
            DB::commit();

            if ($gagalDihapus > 0) {
                return redirect()->route('master.customer.index')->with('sukses', "{$berhasilDihapus} data customer berhasil dihapus. {$gagalDihapus} customer dilewati karena masih memiliki saldo piutang aktif.");
            }

            return redirect()->route('master.customer.index')->with('sukses', "{$berhasilDihapus} data customer terpilih berhasil dihapus.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('master.customer.index')->with('gagal', 'Terjadi kesalahan saat menghapus data massal: ' . $th->getMessage());
        }
    }

    /**
     * API Generator Kode Otomatis
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->query('mode', 'gap');
        if ($mode === 'acak') {
            $kode = 'CUST-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $keterangan = 'Kode acak alfanumerik';
        } else {
            $kode = GeneratorKodeOtomatis::buatKode('data_customer', 'kode_customer', 'CUST-', 3);
            $keterangan = 'Nomor urut terkecil yang tersedia (Gap-Filling)';
        }

        return response()->json([
            'status'     => 'sukses',
            'kode'       => $kode,
            'keterangan' => $keterangan,
        ]);
    }
}
