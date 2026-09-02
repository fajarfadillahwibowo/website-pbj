<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Customer;
use App\Models\Master\Wilayah;
use App\Helpers\GeneratorKodeOtomatis;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar master customer toko bangunan beserta filter & pencarian.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterWilayah = $request->input('wilayah');

        $query = Customer::with('wilayah');

        if ($filterWilayah) {
            $query->where('kode_wilayah', $filterWilayah);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_customer', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp', 'like', "%{$kataKunci}%");
            });
        }

        $daftarCustomer = $query->orderBy('kode_customer', 'asc')->get();
        $daftarWilayah = Wilayah::orderBy('nama_wilayah', 'asc')->get();

        $totalCustomer = Customer::count();
        $totalPlafon = Customer::sum('plafon_piutang');
        $totalPiutang = Customer::sum('saldo_piutang');
        $totalDeposit = Customer::sum('saldo_deposit');

        // Generator kode otomatis gap-filling
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_customer', 'kode_customer', 'CUST-', 3);

        return view('master.customer.index', compact(
            'daftarCustomer',
            'daftarWilayah',
            'kataKunci',
            'filterWilayah',
            'totalCustomer',
            'totalPlafon',
            'totalPiutang',
            'totalDeposit',
            'kodeOtomatis'
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
            'nama_toko_bangunan' => 'required|string|max:150',
            'nama_pemilik'       => 'required|string|max:100',
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
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'nama_pemilik'       => $request->nama_pemilik,
            'alamat'             => $request->alamat,
            'no_hp'              => $request->no_hp,
            'no_ktp'             => $request->no_ktp ?? null,
            'plafon_piutang'     => $request->plafon_piutang,
            'saldo_piutang'      => 0.00,
            'saldo_deposit'      => $request->saldo_deposit ?? 0.00,
        ]);

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$request->nama_toko_bangunan}' berhasil ditambahkan.");
    }

    /**
     * Perbarui data customer.
     */
    public function update(Request $request, $kode_customer)
    {
        $customer = Customer::findOrFail($kode_customer);

        $request->validate([
            'kode_wilayah'       => 'required|string|exists:data_wilayah,kode_wilayah',
            'nama_toko_bangunan' => 'required|string|max:150',
            'nama_pemilik'       => 'required|string|max:100',
            'alamat'             => 'required|string',
            'no_hp'              => 'required|string|max:25',
            'plafon_piutang'     => 'required|numeric|min:0',
        ]);

        $customer->update([
            'kode_wilayah'       => $request->kode_wilayah,
            'nama_toko_bangunan' => $request->nama_toko_bangunan,
            'nama_pemilik'       => $request->nama_pemilik,
            'alamat'             => $request->alamat,
            'no_hp'              => $request->no_hp,
            'no_ktp'             => $request->no_ktp ?? $customer->no_ktp,
            'plafon_piutang'     => $request->plafon_piutang,
        ]);

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$customer->nama_toko_bangunan}' berhasil diperbarui.");
    }

    /**
     * Hapus data customer jika tidak memiliki riwayat transaksi aktif.
     */
    public function destroy($kode_customer)
    {
        $customer = Customer::findOrFail($kode_customer);

        if ($customer->saldo_piutang > 0) {
            return redirect()->route('master.customer.index')->with('gagal', "Customer '{$customer->nama_toko_bangunan}' tidak dapat dihapus karena masih memiliki saldo piutang berjalan.");
        }

        $customer->delete();

        return redirect()->route('master.customer.index')->with('sukses', "Data Customer '{$customer->nama_toko_bangunan}' berhasil dihapus.");
    }

    /**
     * Generator Kode Customer Otomatis (Daur Ulang Slot vs Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return GeneratorKodeOtomatis::responJson('data_customer', 'kode_customer', 'CUST-', $mode, 3);
    }
}
