<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan\KodeAkun;

class KodeAkunController extends Controller
{
    /**
     * Tampilkan daftar Bagan Akun Standar (COA - Chart of Accounts).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterTipe = $request->input('tipe');

        $query = KodeAkun::query();

        if ($filterTipe) {
            $query->where('tipe_akun', $filterTipe);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('kode_akun', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_akun', 'like', "%{$kataKunci}%")
                  ->orWhere('kelompok_akun', 'like', "%{$kataKunci}%");
            });
        }

        $daftarAkun = $query->orderBy('kode_akun', 'asc')->get();

        $totalAkun = KodeAkun::count();
        $totalAktiva = KodeAkun::whereIn('tipe_akun', ['Aktiva Lancar', 'Aktiva Tetap'])->sum('saldo_berjalan');
        $totalKewajiban = KodeAkun::whereIn('tipe_akun', ['Kewajiban Lancar', 'Kewajiban Jangka Panjang'])->sum('saldo_berjalan');
        $totalModal = KodeAkun::where('tipe_akun', 'Modal')->sum('saldo_berjalan');

        return view('keuangan.akuntansi.kode_akun', compact(
            'daftarAkun',
            'kataKunci',
            'filterTipe',
            'totalAkun',
            'totalAktiva',
            'totalKewajiban',
            'totalModal'
        ));
    }

    /**
     * Simpan akun COA baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_akun'     => 'required|string|max:30|unique:data_kode_akun,kode_akun',
            'nama_akun'     => 'required|string|max:100',
            'tipe_akun'     => 'required|string',
            'kelompok_akun' => 'required|string|max:50',
            'saldo_normal'  => 'required|string|in:Debit,Kredit',
            'saldo'         => 'nullable|numeric|min:0',
            'saldo_awal'    => 'nullable|numeric|min:0',
        ], [
            'kode_akun.required' => 'Kode akun wajib diisi.',
            'kode_akun.unique'   => 'Kode akun sudah terdaftar.',
            'nama_akun.required' => 'Nama akun wajib diisi.',
            'tipe_akun.required' => 'Tipe akun wajib dipilih.',
            'kelompok_akun.required' => 'Kelompok akun wajib diisi.',
        ]);

        $saldoNominal = (float) ($request->saldo ?? $request->saldo_awal ?? 0);

        KodeAkun::create([
            'kode_akun'      => $request->kode_akun,
            'nama_akun'      => $request->nama_akun,
            'tipe_akun'      => $request->tipe_akun,
            'kelompok_akun'  => $request->kelompok_akun,
            'saldo_normal'   => $request->saldo_normal,
            'saldo_awal'     => $saldoNominal,
            'saldo_berjalan' => $saldoNominal,
        ]);

        return redirect()->route('keuangan.akuntansi.kode_akun')->with('sukses', "Akun COA [{$request->kode_akun}] '{$request->nama_akun}' berhasil ditambahkan.");
    }

    /**
     * Perbarui akun COA.
     */
    public function update(Request $request, $kode_akun)
    {
        $akun = KodeAkun::findOrFail($kode_akun);

        $request->validate([
            'nama_akun'     => 'required|string|max:100',
            'tipe_akun'     => 'required|string',
            'kelompok_akun' => 'required|string|max:50',
            'saldo_normal'  => 'required|string|in:Debit,Kredit',
            'saldo'         => 'nullable|numeric|min:0',
        ]);

        $dataUpdate = [
            'nama_akun'     => $request->nama_akun,
            'tipe_akun'     => $request->tipe_akun,
            'kelompok_akun' => $request->kelompok_akun,
            'saldo_normal'  => $request->saldo_normal,
        ];

        if ($request->has('saldo') && $request->saldo !== null) {
            $dataUpdate['saldo_berjalan'] = (float) $request->saldo;
        }

        $akun->update($dataUpdate);

        return redirect()->route('keuangan.akuntansi.kode_akun')->with('sukses', "Data Akun COA [{$akun->kode_akun}] berhasil diperbarui.");
    }

    /**
     * Hapus akun COA.
     */
    public function destroy($kode_akun)
    {
        $akun = KodeAkun::findOrFail($kode_akun);

        $adaJurnal = \Illuminate\Support\Facades\DB::table('jurnal_umum')->where('kode_akun', $kode_akun)->exists();
        if ($adaJurnal) {
            return redirect()->route('keuangan.akuntansi.kode_akun')->with('gagal', "Akun COA [{$kode_akun}] tidak dapat dihapus karena telah memiliki riwayat posting jurnal umum.");
        }

        $akun->delete();

        return redirect()->route('keuangan.akuntansi.kode_akun')->with('sukses', "Akun COA [{$akun->kode_akun}] berhasil dihapus.");
    }

    /**
     * Generator Kode Akun COA Otomatis (Daur Ulang Slot vs Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        return \App\Helpers\GeneratorKodeOtomatis::responJson('data_kode_akun', 'kode_akun', '1', $mode, 3, false);
    }
}
