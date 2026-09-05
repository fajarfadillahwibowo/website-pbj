<?php

namespace App\Http\Controllers\Operasional\Armada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Kendaraan;
use App\Models\Operasional\DataKendaraan;
use App\Models\Master\JenisAset;
use App\Helpers\GeneratorKodeOtomatis;
use Carbon\Carbon;

class KendaraanController extends Controller
{
    /**
     * Tampilkan halaman data master kendaraan truk armada ekspedisi & data jenis aset (Tab Terpadu).
     */
    public function index(Request $request)
    {
        // Pastikan master jenis aset memiliki data dasar
        $this->pastikanJenisAsetTersedia();

        $tabAktif = $request->input('tab', 'kendaraan');
        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');
        $jenisFilter = $request->input('jenis', 'semua');

        // Query Data Kendaraan
        $queryKendaraan = Kendaraan::with('jenisAset');

        if (!empty($kataKunci) && $tabAktif === 'kendaraan') {
            $queryKendaraan->where(function ($q) use ($kataKunci) {
                $q->where('nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('no_polisi', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('merek_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('no_mesin', 'like', "%{$kataKunci}%")
                  ->orWhere('no_rangka', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%")
                  ->orWhere('muatan', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $queryKendaraan->where('status_aset', $statusFilter);
        }

        if ($jenisFilter !== 'semua' && !empty($jenisFilter)) {
            $queryKendaraan->where('kode_jenis_aset', $jenisFilter);
        }

        $daftarKendaraan = $queryKendaraan->orderBy('dibuat_pada', 'desc')->get();

        // 4 KPI Statistik Kendaraan
        $semuaKendaraan = Kendaraan::all();
        $totalKendaraan = $semuaKendaraan->count();
        $kendaraanAktif = $semuaKendaraan->where('status_aset', 'aktif')->count();
        $kendaraanServis = $semuaKendaraan->whereIn('status_aset', ['dalam_perbaikan', 'rusak'])->count();

        // Hitung kendaraan dengan KIR/Pajak mendekati jatuh tempo (<= 30 hari atau lewat)
        $batasPeringatan = Carbon::today()->addDays(30);
        $kendaraanPerhatianPajakKir = $semuaKendaraan->filter(function ($k) use ($batasPeringatan) {
            $kirPerhatian = $k->tanggal_kir && Carbon::parse($k->tanggal_kir)->lte($batasPeringatan);
            $pajakPerhatian = $k->tanggal_pajak && Carbon::parse($k->tanggal_pajak)->lte($batasPeringatan);
            return $kirPerhatian || $pajakPerhatian;
        })->count();

        // Query Data Jenis Aset (Kategori)
        $queryJenis = JenisAset::withCount('kendaraan');
        if (!empty($kataKunci) && $tabAktif === 'jenis_aset') {
            $queryJenis->where(function ($q) use ($kataKunci) {
                $q->where('jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_jenis_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan', 'like', "%{$kataKunci}%");
            });
        }
        $daftarJenisAset = $queryJenis->orderBy('dibuat_pada', 'desc')->get();
        $totalJenisAset = JenisAset::count();

        // Statistik Aset Perusahaan (persis seperti di sidebar Aset Perusahaan SPV Keuangan)
        $totalNilaiAset = DB::table('data_aset')->sum('harga_aset');
        $totalAset = DB::table('data_aset')->count();
        $totalTrukAktif = DB::table('data_aset')->whereNotNull('no_polisi')->where('no_polisi', '!=', '-')->count();

        // Generator kode aset otomatis
        $kodeAsetOtomatis = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);

        // Query Data Aset Perusahaan untuk Tab Jenis Aset
        $queryAsetPerusahaan = DB::table('data_aset')
            ->leftJoin('data_jenis_aset', 'data_aset.kode_jenis_aset', '=', 'data_jenis_aset.kode_jenis_aset')
            ->select('data_aset.*', 'data_jenis_aset.jenis_aset');

        if (!empty($kataKunci) && $tabAktif === 'jenis_aset') {
            $queryAsetPerusahaan->where(function ($q) use ($kataKunci) {
                $q->where('data_aset.nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('data_aset.no_polisi', 'like', "%{$kataKunci}%");
            });
        }

        if ($jenisFilter !== 'semua' && !empty($jenisFilter) && $tabAktif === 'jenis_aset') {
            $queryAsetPerusahaan->where('data_aset.kode_jenis_aset', $jenisFilter);
        }

        $daftarAsetPerusahaan = $queryAsetPerusahaan->orderBy('data_aset.kode_aset', 'asc')->get();
        $daftarSemuaJenis = DB::table('data_jenis_aset')->orderBy('jenis_aset')->get();

        return view('operasional.armada.kendaraan', compact(
            'tabAktif',
            'daftarKendaraan',
            'daftarJenisAset',
            'daftarAsetPerusahaan',
            'daftarSemuaJenis',
            'kataKunci',
            'statusFilter',
            'jenisFilter',
            'totalKendaraan',
            'kendaraanAktif',
            'kendaraanServis',
            'kendaraanPerhatianPajakKir',
            'totalJenisAset',
            'totalNilaiAset',
            'totalAset',
            'totalTrukAktif',
            'kodeAsetOtomatis'
        ));
    }

    /**
     * Simpan data kendaraan armada baru ke database.
     */
    public function simpan(Request $request)
    {
        $this->pastikanJenisAsetTersedia();

        $jumlahUnit = max(1, min(50, (int) ($request->jumlah_unit ?? 1)));

        // Normalisasi input jika form mengirim nama atribut alternatif
        if ($request->filled('kode_aset') && str_starts_with($request->kode_aset, 'KND-')) {
            if (!$request->filled('kode_kendaraan')) {
                $request->merge(['kode_kendaraan' => $request->kode_aset]);
            }
            $request->merge(['kode_aset' => null]);
        }

        if (!$request->filled('merek_kendaraan') && $request->filled('merek_aset')) {
            $request->merge(['merek_kendaraan' => $request->merek_aset]);
        }

        if (!$request->filled('status_kendaraan') && $request->filled('status_aset')) {
            $request->merge(['status_kendaraan' => $request->status_aset]);
        }

        if ($request->input('status_kendaraan') === 'dijual') {
            $request->merge(['status_kendaraan' => 'non-aktif']);
        }

        if (!$request->filled('nama_pemilik')) {
            $request->merge(['nama_pemilik' => 'PT Putra Balkom Jaya']);
        }

        $pesanKustom = [
            'kode_kendaraan.unique' => 'Kode armada kendaraan sudah terdaftar di sistem.',
            'kode_kendaraan.max' => 'Kode kendaraan maksimal 30 karakter.',
            'kode_aset.max' => 'Kode aset maksimal 30 karakter.',
            'no_polisi.required' => 'Nomor plat polisi wajib diisi.',
            'no_polisi.max' => 'Nomor plat polisi maksimal 20 karakter.',
            'merek_kendaraan.required' => 'Merek armada truk wajib diisi.',
            'merek_kendaraan.max' => 'Merek armada maksimal 50 karakter.',
            'muatan.required' => 'Kapasitas muatan wajib diisi.',
            'muatan.max' => 'Kapasitas muatan maksimal 50 karakter.',
            'tahun_pembuatan.required' => 'Tahun pembuatan armada wajib diisi.',
            'tahun_pembuatan.integer' => 'Tahun pembuatan armada harus berupa angka tahun.',
            'tahun_pembuatan.min' => 'Tahun pembuatan armada minimal tahun 1990.',
            'tahun_pembuatan.max' => 'Tahun pembuatan armada maksimal tahun 2099.',
            'status_kendaraan.required' => 'Status operasional armada wajib dipilih.',
            'status_kendaraan.in' => 'Status operasional armada yang dipilih tidak valid.',
            'nama_pemilik.required' => 'Nama pemilik armada wajib diisi.',
            'nama_pemilik.max' => 'Nama pemilik armada maksimal 100 karakter.',
            'harga_aset.numeric' => 'Nilai perolehan unit harus berupa angka nominal.',
            'harga_aset.min' => 'Nilai perolehan unit tidak boleh bernilai negatif.',
            'harga_aset.max' => 'Nilai perolehan unit maksimal Rp 9.999.999.999.999 (9,9 Triliun).',
            'jumlah_unit.integer' => 'Jumlah unit harus berupa angka bulat.',
            'jumlah_unit.min' => 'Jumlah unit minimal 1 unit.',
            'jumlah_unit.max' => 'Jumlah unit maksimal 50 unit sekaligus.',
            'tanggal_kir.date' => 'Format tanggal uji KIR Dishub tidak valid.',
            'tanggal_pajak.date' => 'Format tanggal jatuh tempo pajak STNK tidak valid.',
        ];

        $validated = $request->validate([
            'kode_kendaraan' => 'nullable|string|max:30|unique:data_kendaraan,kode_kendaraan',
            'no_polisi' => 'required|string|max:20',
            'kode_aset' => 'nullable|string|max:30',
            'nama_aset' => 'nullable|string|max:100',
            'merek_kendaraan' => 'required|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'tipe_armada' => 'nullable|string|max:50',
            'muatan' => 'required|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'required|integer|min:1990|max:2099',
            'tanggal_kir' => 'nullable|date',
            'tanggal_pajak' => 'nullable|date',
            'status_kendaraan' => 'required|in:aktif,rusak,dalam_perbaikan,non-aktif',
            'nama_pemilik' => 'required|string|max:100',
            'harga_aset' => 'nullable|numeric|min:0|max:9999999999999',
            'jumlah_unit' => 'nullable|integer|min:1|max:50',
        ], $pesanKustom);

        DB::beginTransaction();
        try {
            $hargaBeli = (float) ($validated['harga_aset'] ?? 0);
            $namaModelDasar = !empty($validated['nama_aset']) 
                ? $validated['nama_aset'] 
                : ($validated['merek_kendaraan'] . ' ' . (!empty($validated['jenis_kendaraan']) ? $validated['jenis_kendaraan'] : 'Truk'));

            $daftarKodeKendaraan = ($jumlahUnit === 1 && $request->filled('kode_kendaraan'))
                ? [strtoupper(trim($request->kode_kendaraan))]
                : GeneratorKodeOtomatis::buatBanyakKode('data_kendaraan', 'kode_kendaraan', 'KND-', $jumlahUnit, 3);

            $daftarKodeAset = GeneratorKodeOtomatis::buatBanyakKode('data_aset', 'kode_aset', 'AST-', $jumlahUnit, 3);
            $rincianUnitInput = $request->input('rincian_unit', []);

            for ($i = 0; $i < $jumlahUnit; $i++) {
                $nomorUrut = $i + 1;
                $kodeKendaraan = $daftarKodeKendaraan[$i];
                $kodeAset = $daftarKodeAset[$i];

                $rincian = $rincianUnitInput[$i] ?? [];
                $plat = !empty($rincian['no_polisi']) 
                    ? strtoupper(trim($rincian['no_polisi'])) 
                    : ($i === 0 ? strtoupper(trim($validated['no_polisi'])) : ('B ' . (9100 + $i) . ' PBJ'));
                $mesin = !empty($rincian['no_mesin']) ? strtoupper(trim($rincian['no_mesin'])) : (!empty($validated['no_mesin']) ? strtoupper(trim($validated['no_mesin'])) : '-');
                $rangka = !empty($rincian['no_rangka']) ? strtoupper(trim($rincian['no_rangka'])) : (!empty($validated['no_rangka']) ? strtoupper(trim($validated['no_rangka'])) : '-');

                $namaModel = $jumlahUnit > 1 ? ($namaModelDasar . ' #' . str_pad($nomorUrut, 2, '0', STR_PAD_LEFT)) : $namaModelDasar;

                $tglBeli = $request->filled('tanggal_pembelian') 
                    ? Carbon::parse($request->input('tanggal_pembelian'))->format('Y-m-d') 
                    : now()->format('Y-m-d');
                $tglKir = !empty($validated['tanggal_kir']) 
                    ? Carbon::parse($validated['tanggal_kir'])->format('Y-m-d') 
                    : null;
                $tglPajak = !empty($validated['tanggal_pajak']) 
                    ? Carbon::parse($validated['tanggal_pajak'])->format('Y-m-d') 
                    : null;

                // 1. Catat entitas aset finansial di data_aset jika belum ada
                $adaAset = DB::table('data_aset')->where('kode_aset', $kodeAset)->exists();
                if (!$adaAset) {
                    $kodeAkunAset = DB::table('data_kode_akun')->where('kode_akun', '1201')->exists() ? '1201' : null;
                    $kodeAkunAkum = DB::table('data_kode_akun')->where('kode_akun', '1202')->exists() ? '1202' : null;
                    $kodeAkunBeban = DB::table('data_kode_akun')->where('kode_akun', '6105')->exists() ? '6105' : null;

                    $kodeJenisAset = $request->input('kode_jenis_aset', 'AST-TRK');
                    if (!DB::table('data_jenis_aset')->where('kode_jenis_aset', $kodeJenisAset)->exists()) {
                        $kodeJenisAset = DB::table('data_jenis_aset')->where('kode_jenis_aset', 'AST-TRK')->value('kode_jenis_aset')
                            ?? DB::table('data_jenis_aset')->value('kode_jenis_aset');
                    }

                    DB::table('data_aset')->insert([
                        'kode_aset'            => $kodeAset,
                        'kode_jenis_aset'      => $kodeJenisAset,
                        'nama_aset'            => $namaModel,
                        'tanggal_pembelian'    => $tglBeli,
                        'harga_aset'           => $hargaBeli,
                        'harga_perolehan'      => $hargaBeli,
                        'nilai_residu'         => 0.00,
                        'umur_manfaat'         => 8,
                        'metode_penyusutan'    => 'Garis Lurus',
                        'tarif_penyusutan'     => 12.50,
                        'kode_akun_aset'       => $kodeAkunAset,
                        'kode_akun_akumulasi'  => $kodeAkunAkum,
                        'kode_akun_beban'      => $kodeAkunBeban,
                        'akumulasi_penyusutan' => 0.00,
                        'nilai_buku'           => $hargaBeli,
                        'status_aset'          => $validated['status_kendaraan'],
                        'nama_pemilik'         => $validated['nama_pemilik'],
                        'no_polisi'            => $plat,
                        'no_mesin'             => $mesin,
                        'no_rangka'            => $rangka,
                        'merek_aset'           => trim($validated['merek_kendaraan']),
                        'muatan'               => trim($validated['muatan']),
                        'jenis_kendaraan'      => !empty($validated['jenis_kendaraan']) ? trim($validated['jenis_kendaraan']) : 'Colt Diesel Double',
                        'tahun_pembuatan'      => $validated['tahun_pembuatan'],
                        'tanggal_kir'          => $tglKir,
                        'tanggal_pajak'        => $tglPajak,
                        'dibuat_pada'          => now(),
                        'diperbarui_pada'      => now(),
                    ]);
                }

                // 2. Simpan ke data_kendaraan
                Kendaraan::create([
                    'kode_kendaraan'   => $kodeKendaraan,
                    'kode_aset'        => $kodeAset,
                    'no_polisi'        => $plat,
                    'no_mesin'         => $mesin !== '-' ? $mesin : null,
                    'no_rangka'        => $rangka !== '-' ? $rangka : null,
                    'merek_kendaraan'  => trim($validated['merek_kendaraan']),
                    'jenis_kendaraan'  => !empty($validated['jenis_kendaraan']) ? trim($validated['jenis_kendaraan']) : 'Colt Diesel Double',
                    'tipe_armada'      => !empty($validated['tipe_armada']) ? trim($validated['tipe_armada']) : (!empty($validated['jenis_kendaraan']) ? trim($validated['jenis_kendaraan']) : 'Colt Diesel Double'),
                    'muatan'           => trim($validated['muatan']),
                    'tahun_pembuatan'  => $validated['tahun_pembuatan'],
                    'tanggal_kir'      => $tglKir,
                    'tanggal_pajak'    => $tglPajak,
                    'status_kendaraan' => $validated['status_kendaraan'],
                    'nama_pemilik'     => trim($validated['nama_pemilik']),
                ]);
            }

            DB::commit();

            $pesan = $jumlahUnit > 1
                ? "Berhasil menambahkan {$jumlahUnit} unit armada sekaligus (Kode: {$daftarKodeKendaraan[0]} s/d {$daftarKodeKendaraan[$jumlahUnit-1]})."
                : "Data armada [{$daftarKodeKendaraan[0]}] berhasil ditambahkan ke database!";

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])->with('sukses', $pesan);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan armada: ' . $e->getMessage());
        }
    }

    /**
     * Ambil data detail kendaraan dalam format JSON untuk modal Alpine.js.
     */
    public function ambilDetail($id)
    {
        $kendaraan = Kendaraan::with(['asetPerusahaan.jenisAset'])
            ->where('kode_kendaraan', $id)
            ->orWhere('kode_aset', $id)
            ->first();

        if (!$kendaraan) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data kendaraan tidak ditemukan di sistem.'
            ], 404);
        }

        $dataKendaraan = $kendaraan->toArray();
        $dataKendaraan['kode_kendaraan'] = $kendaraan->kode_kendaraan;
        $dataKendaraan['kode_aset'] = $kendaraan->kode_aset ?? $kendaraan->kode_kendaraan;
        $dataKendaraan['merek_kendaraan'] = $kendaraan->merek_kendaraan;
        $dataKendaraan['merek_aset'] = $kendaraan->merek_kendaraan ?: ($kendaraan->asetPerusahaan->merek_aset ?? '');
        $dataKendaraan['status_kendaraan'] = $kendaraan->status_kendaraan;
        $dataKendaraan['status_aset'] = $kendaraan->status_kendaraan ?: ($kendaraan->asetPerusahaan->status_aset ?? 'aktif');
        $dataKendaraan['kode_jenis_aset'] = $kendaraan->asetPerusahaan->kode_jenis_aset ?? 'AST-TRK';
        $dataKendaraan['harga_aset'] = (int) round((float) ($kendaraan->asetPerusahaan->harga_perolehan ?? ($kendaraan->asetPerusahaan->harga_aset ?? 0)));
        $dataKendaraan['tanggal_pembelian'] = !empty($kendaraan->asetPerusahaan?->tanggal_pembelian)
            ? Carbon::parse($kendaraan->asetPerusahaan->tanggal_pembelian)->format('Y-m-d')
            : '';
        $dataKendaraan['tanggal_kir'] = !empty($kendaraan->tanggal_kir)
            ? Carbon::parse($kendaraan->tanggal_kir)->format('Y-m-d')
            : '';
        $dataKendaraan['tanggal_pajak'] = !empty($kendaraan->tanggal_pajak)
            ? Carbon::parse($kendaraan->tanggal_pajak)->format('Y-m-d')
            : '';

        return response()->json([
            'status' => 'sukses',
            'data' => $dataKendaraan
        ]);
    }

    /**
     * Perbarui data kendaraan armada di database.
     */
    public function perbarui(Request $request, $id)
    {
        $kendaraan = Kendaraan::where('kode_kendaraan', $id)
            ->orWhere('kode_aset', $id)
            ->first();

        if (!$kendaraan) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', 'Data armada kendaraan tidak ditemukan.');
        }

        // Normalisasi input form jika mengirim format nama field alternatif
        if (!$request->filled('merek_kendaraan') && $request->filled('merek_aset')) {
            $request->merge(['merek_kendaraan' => $request->merek_aset]);
        }

        if (!$request->filled('status_kendaraan') && $request->filled('status_aset')) {
            $request->merge(['status_kendaraan' => $request->status_aset]);
        }

        if ($request->input('status_kendaraan') === 'dijual') {
            $request->merge(['status_kendaraan' => 'non-aktif']);
        }

        if (!$request->filled('nama_pemilik')) {
            $request->merge(['nama_pemilik' => $kendaraan->nama_pemilik ?? 'PT Putra Balkom Jaya']);
        }

        $pesanKustom = [
            'no_polisi.required' => 'Nomor plat polisi wajib diisi.',
            'no_polisi.max' => 'Nomor plat polisi maksimal 20 karakter.',
            'merek_kendaraan.required' => 'Merek armada truk wajib diisi.',
            'merek_kendaraan.max' => 'Merek armada maksimal 50 karakter.',
            'muatan.required' => 'Kapasitas muatan wajib diisi.',
            'muatan.max' => 'Kapasitas muatan maksimal 50 karakter.',
            'tahun_pembuatan.required' => 'Tahun pembuatan armada wajib diisi.',
            'tahun_pembuatan.integer' => 'Tahun pembuatan armada harus berupa angka tahun.',
            'tahun_pembuatan.min' => 'Tahun pembuatan armada minimal tahun 1990.',
            'tahun_pembuatan.max' => 'Tahun pembuatan armada maksimal tahun 2099.',
            'status_kendaraan.required' => 'Status operasional armada wajib dipilih.',
            'status_kendaraan.in' => 'Status operasional armada yang dipilih tidak valid.',
            'nama_pemilik.required' => 'Nama pemilik armada wajib diisi.',
            'nama_pemilik.max' => 'Nama pemilik armada maksimal 100 karakter.',
            'tanggal_kir.date' => 'Format tanggal uji KIR Dishub tidak valid.',
            'tanggal_pajak.date' => 'Format tanggal jatuh tempo pajak STNK tidak valid.',
            'harga_aset.numeric' => 'Harga perolehan armada harus berupa angka.',
            'harga_aset.max' => 'Nominal harga perolehan armada maksimal Rp 9.999.999.999.999.',
        ];

        $validated = $request->validate([
            'no_polisi' => 'required|string|max:20',
            'nama_aset' => 'nullable|string|max:100',
            'merek_kendaraan' => 'required|string|max:50',
            'jenis_kendaraan' => 'nullable|string|max:50',
            'tipe_armada' => 'nullable|string|max:50',
            'muatan' => 'required|string|max:50',
            'no_mesin' => 'nullable|string|max:50',
            'no_rangka' => 'nullable|string|max:50',
            'tahun_pembuatan' => 'required|integer|min:1990|max:2099',
            'tanggal_kir' => 'nullable|date',
            'tanggal_pajak' => 'nullable|date',
            'status_kendaraan' => 'required|in:aktif,rusak,dalam_perbaikan,non-aktif',
            'nama_pemilik' => 'required|string|max:100',
            'harga_aset' => 'nullable|numeric|min:0|max:9999999999999',
        ], $pesanKustom);

        $tglKir = !empty($validated['tanggal_kir'])
            ? Carbon::parse($validated['tanggal_kir'])->format('Y-m-d')
            : null;
        $tglPajak = !empty($validated['tanggal_pajak'])
            ? Carbon::parse($validated['tanggal_pajak'])->format('Y-m-d')
            : null;

        DB::beginTransaction();
        try {
            $kendaraan->update([
                'no_polisi'        => strtoupper(trim($validated['no_polisi'])),
                'no_mesin'         => !empty($validated['no_mesin']) ? strtoupper(trim($validated['no_mesin'])) : $kendaraan->no_mesin,
                'no_rangka'        => !empty($validated['no_rangka']) ? strtoupper(trim($validated['no_rangka'])) : $kendaraan->no_rangka,
                'merek_kendaraan'  => trim($validated['merek_kendaraan']),
                'jenis_kendaraan'  => !empty($validated['jenis_kendaraan']) ? trim($validated['jenis_kendaraan']) : $kendaraan->jenis_kendaraan,
                'tipe_armada'      => !empty($validated['tipe_armada']) ? trim($validated['tipe_armada']) : $kendaraan->tipe_armada,
                'muatan'           => trim($validated['muatan']),
                'tahun_pembuatan'  => $validated['tahun_pembuatan'],
                'tanggal_kir'      => $tglKir,
                'tanggal_pajak'    => $tglPajak,
                'status_kendaraan' => $validated['status_kendaraan'],
                'nama_pemilik'     => trim($validated['nama_pemilik']),
                'diperbarui_pada'  => now(),
            ]);

            // Sinkronkan ke data_aset jika berelasi
            if ($kendaraan->kode_aset) {
                $asetUpdate = [
                    'no_polisi'       => strtoupper(trim($validated['no_polisi'])),
                    'status_aset'     => $validated['status_kendaraan'],
                    'merek_aset'      => trim($validated['merek_kendaraan']),
                    'muatan'          => trim($validated['muatan']),
                    'tahun_pembuatan' => $validated['tahun_pembuatan'],
                    'tanggal_kir'     => $tglKir,
                    'tanggal_pajak'   => $tglPajak,
                    'diperbarui_pada' => now(),
                ];
                if (!empty($validated['nama_aset'])) {
                    $asetUpdate['nama_aset'] = trim($validated['nama_aset']);
                }
                if ($request->filled('harga_aset')) {
                    $asetUpdate['harga_aset'] = (float) $request->harga_aset;
                    $asetUpdate['harga_perolehan'] = (float) $request->harga_aset;
                }
                if ($request->filled('tanggal_pembelian')) {
                    $asetUpdate['tanggal_pembelian'] = Carbon::parse($request->tanggal_pembelian)->format('Y-m-d');
                }
                if ($request->filled('kode_jenis_aset')) {
                    $asetUpdate['kode_jenis_aset'] = $request->kode_jenis_aset;
                }
                DB::table('data_aset')->where('kode_aset', $kendaraan->kode_aset)->update($asetUpdate);
            }

            DB::commit();

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('sukses', "Data armada [{$kendaraan->kode_kendaraan}] ({$kendaraan->no_polisi}) berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui armada: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data kendaraan dari database.
     */
    public function hapus(Request $request, $id = null)
    {
        $targetId = $id ?: $request->input('kode_kendaraan', $request->input('kode_aset'));

        if (!$targetId) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', 'Kode armada kendaraan tidak ditemukan atau belum dipilih.');
        }

        $kendaraan = Kendaraan::where('kode_kendaraan', $targetId)
            ->orWhere('kode_aset', $targetId)
            ->first();

        if (!$kendaraan) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', "Data armada [{$targetId}] tidak ditemukan atau sudah dihapus sebelumnya.");
        }

        $noPolisi = $kendaraan->no_polisi;
        $kodeKnd = $kendaraan->kode_kendaraan;
        $kodeAset = $kendaraan->kode_aset;

        DB::beginTransaction();
        try {
            $kendaraan->delete();

            if (!empty($kodeAset)) {
                DB::table('data_aset')->where('kode_aset', $kodeAset)->delete();
            }

            DB::commit();

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('sukses', "Data armada [{$kodeKnd}] ({$noPolisi}) berhasil dihapus dari database!");
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', "Gagal menghapus armada [{$kodeKnd}]! Data kendaraan ini masih terikat dengan dokumen Surat Jalan atau SPK Servis Bengkel.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'kendaraan'])
                ->with('error', "Gagal menghapus armada: " . $e->getMessage());
        }
    }

    /**
     * Helper endpoint generator kode kendaraan otomatis (Gap-filling & Acak).
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 4; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'KND-' . $acak;
                $sudahAda = DB::table('data_kendaraan')->where('kode_kendaraan', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('KND-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Kode Alfanumerik Acak (Anti-Tebak)'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarKode = DB::table('data_kendaraan')
            ->where('kode_kendaraan', 'like', 'KND-%')
            ->pluck('kode_kendaraan');

        $nomorTerpakai = [];
        foreach ($daftarKode as $kode) {
            if (preg_match('/KND-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'KND-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    // =========================================================================
    // SUB-FITUR: CRUD DATA JENIS ASET DI DALAM DATA KENDARAAN
    // =========================================================================

    /**
     * Simpan data jenis aset baru.
     */
    public function simpanJenisAset(Request $request)
    {
        $pesanKustom = [
            'kode_jenis_aset.required' => 'Kode jenis aset wajib diisi.',
            'kode_jenis_aset.unique' => 'Kode jenis aset sudah terdaftar.',
            'jenis_aset.required' => 'Nama kategori jenis aset wajib diisi.',
            'keterangan.required' => 'Keterangan spesifikasi muatan wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_jenis_aset' => 'required|string|max:30|unique:data_jenis_aset,kode_jenis_aset',
            'jenis_aset' => 'required|string|max:100',
            'keterangan' => 'required|string',
        ], $pesanKustom);

        JenisAset::create([
            'kode_jenis_aset' => strtoupper(trim($validated['kode_jenis_aset'])),
            'jenis_aset' => trim($validated['jenis_aset']),
            'keterangan' => trim($validated['keterangan']),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Kategori Jenis Aset {$validated['jenis_aset']} ({$validated['kode_jenis_aset']}) berhasil ditambahkan!");
    }

    /**
     * Ambil data detail jenis aset untuk modal Alpine.js.
     */
    public function ambilDetailJenisAset($kode_jenis_aset)
    {
        $jenisAset = JenisAset::with(['kendaraan'])->where('kode_jenis_aset', $kode_jenis_aset)->first();

        if (!$jenisAset) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data jenis aset tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $jenisAset
        ]);
    }

    /**
     * Perbarui data jenis aset.
     */
    public function perbaruiJenisAset(Request $request, $kode_jenis_aset)
    {
        $jenisAset = JenisAset::where('kode_jenis_aset', $kode_jenis_aset)->firstOrFail();

        $pesanKustom = [
            'jenis_aset.required' => 'Nama kategori jenis aset wajib diisi.',
            'keterangan.required' => 'Keterangan spesifikasi muatan wajib diisi.',
        ];

        $validated = $request->validate([
            'jenis_aset' => 'required|string|max:100',
            'keterangan' => 'required|string',
        ], $pesanKustom);

        $jenisAset->update([
            'jenis_aset' => trim($validated['jenis_aset']),
            'keterangan' => trim($validated['keterangan']),
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Data kategori {$jenisAset->jenis_aset} ({$jenisAset->kode_jenis_aset}) berhasil diperbarui!");
    }

    /**
     * Hapus data jenis aset.
     */
    public function hapusJenisAset($kode_jenis_aset)
    {
        $jenisAset = JenisAset::where('kode_jenis_aset', $kode_jenis_aset)->firstOrFail();
        $namaJenis = $jenisAset->jenis_aset;

        $jumlahUnitTerhubung = Kendaraan::where('kode_jenis_aset', $kode_jenis_aset)->count();
        if ($jumlahUnitTerhubung > 0) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', "Gagal menghapus kategori {$namaJenis}! Terdapat {$jumlahUnitTerhubung} unit armada truk yang masih terhubung ke jenis aset ini.");
        }

        try {
            $jenisAset->delete();

            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('sukses', "Kategori jenis aset {$namaJenis} ({$kode_jenis_aset}) berhasil dihapus dari sistem!");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', "Gagal menghapus jenis aset {$namaJenis}! Data masih memiliki dependensi di sistem.");
        }
    }

    /**
     * Generator kode otomatis untuk jenis aset.
     */
    public function buatKodeJenisAsetOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 4; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'JNS-' . $acak;
                $sudahAda = DB::table('data_jenis_aset')->where('kode_jenis_aset', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('JNS-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Kode Alfanumerik Acak (Anti-Tebak)'
            ]);
        }

        $daftarJenis = DB::table('data_jenis_aset')
            ->where('kode_jenis_aset', 'like', 'JNS-%')
            ->pluck('kode_jenis_aset');

        $nomorTerpakai = [];
        foreach ($daftarJenis as $kode) {
            if (preg_match('/JNS-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'JNS-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Simpan data aset perusahaan baru dari sub-fitur di Data Kendaraan.
     */
    public function simpanAset(Request $request)
    {
        if (!$request->filled('kode_aset')) {
            $request->merge([
                'kode_aset' => GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3)
            ]);
        }

        $pesanKustom = [
            'kode_aset.required' => 'Kode aset wajib diisi.',
            'kode_aset.unique' => 'Kode aset sudah terdaftar.',
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'kode_jenis_aset.exists' => 'Jenis aset tidak valid.',
            'nama_aset.required' => 'Nama aset wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
            'harga_aset.numeric' => 'Harga aset harus berupa angka.',
        ];

        $validated = $request->validate([
            'kode_aset'         => 'required|string|max:30|unique:data_aset,kode_aset',
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'no_polisi'         => 'nullable|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0',
        ], $pesanKustom);

        $tglBeliAset = Carbon::parse($validated['tanggal_pembelian'])->format('Y-m-d');
        $nominalHarga = (float) $validated['harga_aset'];

        // Tentukan akun aset dan parameter penyusutan default berdasarkan jenis aset
        $kodeAkunAset = null;
        $kodeAkunAkum = null;
        $kodeAkunBeban = null;
        $umurManfaat = 8;
        $tarifSusut = 12.50;

        if ($validated['kode_jenis_aset'] === 'AST-TRK') {
            $kodeAkunAset = DB::table('data_kode_akun')->where('kode_akun', '1201')->exists() ? '1201' : null;
            $kodeAkunAkum = DB::table('data_kode_akun')->where('kode_akun', '1202')->exists() ? '1202' : null;
            $kodeAkunBeban = DB::table('data_kode_akun')->where('kode_akun', '6105')->exists() ? '6105' : null;
            $umurManfaat = 8;
            $tarifSusut = 12.50;
        } elseif ($validated['kode_jenis_aset'] === 'AST-GDG') {
            $kodeAkunAset = DB::table('data_kode_akun')->where('kode_akun', '1203')->exists() ? '1203' : null;
            $kodeAkunAkum = DB::table('data_kode_akun')->where('kode_akun', '1208')->exists() ? '1208' : null;
            $kodeAkunBeban = DB::table('data_kode_akun')->where('kode_akun', '6107')->exists() ? '6107' : null;
            $umurManfaat = 20;
            $tarifSusut = 5.00;
        }

        DB::table('data_aset')->insert([
            'kode_aset'            => strtoupper(trim($validated['kode_aset'])),
            'kode_jenis_aset'      => $validated['kode_jenis_aset'],
            'nama_aset'            => trim($validated['nama_aset']),
            'tanggal_pembelian'    => $tglBeliAset,
            'harga_aset'           => $nominalHarga,
            'harga_perolehan'      => $nominalHarga,
            'nilai_residu'         => 0.00,
            'umur_manfaat'         => $umurManfaat,
            'metode_penyusutan'    => 'Garis Lurus',
            'tarif_penyusutan'     => $tarifSusut,
            'kode_akun_aset'       => $kodeAkunAset,
            'kode_akun_akumulasi'  => $kodeAkunAkum,
            'kode_akun_beban'      => $kodeAkunBeban,
            'akumulasi_penyusutan' => 0.00,
            'nilai_buku'           => $nominalHarga,
            'no_polisi'            => !empty($validated['no_polisi']) ? strtoupper(trim($validated['no_polisi'])) : '-',
            'merek_aset'           => $request->merek_aset ?? '-',
            'jenis_kendaraan'      => $request->jenis_kendaraan ?? '-',
            'muatan'               => $request->muatan ?? '-',
            'status_aset'          => $request->status_aset ?? 'aktif',
            'nama_pemilik'         => $request->nama_pemilik ?? 'PT Putra Balkom Jaya',
            'dibuat_pada'          => now(),
            'diperbarui_pada'      => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Aset {$validated['nama_aset']} ({$validated['kode_aset']}) berhasil didaftarkan ke inventaris aset perusahaan!");
    }

    /**
     * Ambil detail data aset untuk modal Alpine.js di Data Kendaraan.
     */
    public function ambilDetailAset($kode_aset)
    {
        $aset = DB::table('data_aset')
            ->leftJoin('data_jenis_aset', 'data_aset.kode_jenis_aset', '=', 'data_jenis_aset.kode_jenis_aset')
            ->select('data_aset.*', 'data_jenis_aset.jenis_aset')
            ->where('data_aset.kode_aset', $kode_aset)
            ->first();

        if (!$aset) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data aset tidak ditemukan.'
            ], 404);
        }

        $dataAset = (array) $aset;
        if (!empty($dataAset['tanggal_pembelian'])) {
            $dataAset['tanggal_pembelian'] = Carbon::parse($dataAset['tanggal_pembelian'])->format('Y-m-d');
        }
        if (!empty($dataAset['tanggal_kir'])) {
            $dataAset['tanggal_kir'] = Carbon::parse($dataAset['tanggal_kir'])->format('Y-m-d');
        }
        if (!empty($dataAset['tanggal_pajak'])) {
            $dataAset['tanggal_pajak'] = Carbon::parse($dataAset['tanggal_pajak'])->format('Y-m-d');
        }
        if (isset($dataAset['harga_aset'])) {
            $dataAset['harga_aset'] = (int) round((float) $dataAset['harga_aset']);
        }
        if (isset($dataAset['harga_perolehan'])) {
            $dataAset['harga_perolehan'] = (int) round((float) $dataAset['harga_perolehan']);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $dataAset
        ]);
    }

    /**
     * Perbarui data aset perusahaan di Data Kendaraan.
     */
    public function perbaruiAset(Request $request, $kode_aset)
    {
        $pesanKustom = [
            'kode_jenis_aset.required' => 'Jenis aset wajib dipilih.',
            'nama_aset.required' => 'Nama aset wajib diisi.',
            'tanggal_pembelian.required' => 'Tanggal pembelian wajib diisi.',
            'harga_aset.required' => 'Harga perolehan aset wajib diisi.',
            'harga_aset.numeric' => 'Harga perolehan aset harus berupa angka.',
            'harga_aset.max' => 'Nominal harga perolehan aset maksimal Rp 9.999.999.999.999.',
        ];

        $validated = $request->validate([
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'no_polisi'         => 'nullable|string|max:20',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0|max:9999999999999',
            'status_aset'       => 'nullable|string|in:aktif,non-aktif,dalam_perbaikan,rusak,dijual',
        ], $pesanKustom);

        $tglBeliAset = Carbon::parse($validated['tanggal_pembelian'])->format('Y-m-d');
        $nominalHarga = (float) $validated['harga_aset'];

        DB::table('data_aset')->where('kode_aset', $kode_aset)->update([
            'kode_jenis_aset'   => $validated['kode_jenis_aset'],
            'nama_aset'         => trim($validated['nama_aset']),
            'tanggal_pembelian' => $tglBeliAset,
            'harga_aset'        => $nominalHarga,
            'harga_perolehan'   => $nominalHarga,
            'nilai_buku'        => DB::raw('GREATEST(0, ' . $nominalHarga . ' - akumulasi_penyusutan)'),
            'no_polisi'         => !empty($validated['no_polisi']) ? strtoupper(trim($validated['no_polisi'])) : '-',
            'status_aset'       => $validated['status_aset'] ?? 'aktif',
            'diperbarui_pada'   => now(),
        ]);

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Data aset {$validated['nama_aset']} ({$kode_aset}) berhasil diperbarui!");
    }

    /**
     * Hapus data aset perusahaan di Data Kendaraan.
     */
    public function hapusAset($kode_aset)
    {
        $aset = DB::table('data_aset')->where('kode_aset', $kode_aset)->first();
        if (!$aset) {
            return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
                ->with('error', 'Data aset tidak ditemukan.');
        }

        DB::table('data_aset')->where('kode_aset', $kode_aset)->delete();

        return redirect()->route('operasional.armada.kendaraan', ['tab' => 'jenis_aset'])
            ->with('sukses', "Aset {$aset->nama_aset} ({$kode_aset}) berhasil dihapus!");
    }

    /**
     * Generator kode otomatis untuk aset perusahaan.
     */
    public function buatKodeAsetOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $acak = '';
            for ($i = 0; $i < 4; $i++) {
                $acak .= $karakter[random_int(0, strlen($karakter) - 1)];
            }
            return response()->json([
                'status' => 'sukses',
                'kode_otomatis' => 'AST-' . $acak,
                'keterangan' => 'Kode Acak Anti-Tebak'
            ]);
        }

        $kodeBaru = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);
        return response()->json([
            'status' => 'sukses',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia'
        ]);
    }

    /**
     * Helper inisialisasi data jenis aset dan data awal jika kosong.
     */
    private function pastikanJenisAsetTersedia(): void
    {
        $defaultJenis = [
            ['kode_jenis_aset' => 'AST-TRK', 'jenis_aset' => 'Armada Truk & Tronton', 'keterangan' => 'Truk armada ekspedisi pengangkut semen (umur 8 tahun, tarif 12.5%)'],
            ['kode_jenis_aset' => 'KND-TRN', 'jenis_aset' => 'Truk Tronton Wingbox', 'keterangan' => 'Kapasitas 25 - 30 Ton (500 - 600 Zak Semen)'],
            ['kode_jenis_aset' => 'KND-CDD', 'jenis_aset' => 'Colt Diesel Double (CDD)', 'keterangan' => 'Kapasitas 10 - 15 Ton (200 - 300 Zak Semen)'],
            ['kode_jenis_aset' => 'KND-TKG', 'jenis_aset' => 'Truk Tangki Semen Curah', 'keterangan' => 'Kapasitas 30 - 35 Ton Semen Curah Bulk'],
            ['kode_jenis_aset' => 'KND-TRL', 'jenis_aset' => 'Truk Trailer Gandeng 40ft', 'keterangan' => 'Kapasitas angkut kontainer & semen muatan besar'],
            ['kode_jenis_aset' => 'KND-PKP', 'jenis_aset' => 'Pick Up / Operasional Lapangan', 'keterangan' => 'Armada operasional supervisor & teknisi'],
        ];

        foreach ($defaultJenis as $j) {
            DB::table('data_jenis_aset')->updateOrInsert(
                ['kode_jenis_aset' => $j['kode_jenis_aset']],
                [
                    'jenis_aset' => $j['jenis_aset'],
                    'keterangan' => $j['keterangan'],
                ]
            );
        }

        // Jika data_aset masih kosong, tambahkan sample data awal
        $jumlahAset = DB::table('data_aset')->count();
        if ($jumlahAset === 0) {
            $contohUnit = [
                [
                    'kode_aset' => 'TRK-001',
                    'kode_jenis_aset' => 'KND-TRN',
                    'nama_aset' => 'Hino 500 Tronton Wingbox FL 260',
                    'tanggal_pembelian' => '2023-03-15',
                    'harga_aset' => 1250000000.00,
                    'no_polisi' => 'B 9283 TDF',
                    'no_mesin' => 'J08E-UG-12948',
                    'no_rangka' => 'MJECFL8J7N-039182',
                    'merek_aset' => 'Hino',
                    'muatan' => '25 Ton (500 Zak)',
                    'jenis_kendaraan' => 'Tronton Wingbox',
                    'tahun_pembuatan' => 2023,
                    'tanggal_kir' => Carbon::today()->addMonths(4)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(7)->format('Y-m-d'),
                    'status_aset' => 'aktif',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
                [
                    'kode_aset' => 'TRK-002',
                    'kode_jenis_aset' => 'KND-CDD',
                    'nama_aset' => 'Mitsubishi Canter FE 74 HD',
                    'tanggal_pembelian' => '2022-07-20',
                    'harga_aset' => 540000000.00,
                    'no_polisi' => 'B 8411 UQ',
                    'no_mesin' => '4D34-T9921',
                    'no_rangka' => 'MHMFE74HD-082194',
                    'merek_aset' => 'Mitsubishi Fuso',
                    'muatan' => '15 Ton (300 Zak)',
                    'jenis_kendaraan' => 'Colt Diesel Double',
                    'tahun_pembuatan' => 2022,
                    'tanggal_kir' => Carbon::today()->addDays(15)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(2)->format('Y-m-d'),
                    'status_aset' => 'aktif',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
                [
                    'kode_aset' => 'TRK-003',
                    'kode_jenis_aset' => 'KND-TKG',
                    'nama_aset' => 'Isuzu GIGA FVM 240 Tangki Curah',
                    'tanggal_pembelian' => '2021-11-10',
                    'harga_aset' => 1400000000.00,
                    'no_polisi' => 'B 9042 KPB',
                    'no_mesin' => '6HK1-TCN-84920',
                    'no_rangka' => 'MP3FVM34N-028491',
                    'merek_aset' => 'Isuzu',
                    'muatan' => '32 Ton Semen Curah',
                    'jenis_kendaraan' => 'Tangki Curah Bulk',
                    'tahun_pembuatan' => 2021,
                    'tanggal_kir' => Carbon::today()->subDays(5)->format('Y-m-d'),
                    'tanggal_pajak' => Carbon::today()->addMonths(5)->format('Y-m-d'),
                    'status_aset' => 'dalam_perbaikan',
                    'nama_pemilik' => 'PT Putra Balkom Jaya',
                ],
            ];

            foreach ($contohUnit as $unit) {
                DB::table('data_aset')->insert(array_merge($unit, [
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ]));
            }
        }

        // Sinkronkan armada yang ada di data_aset ke data_kendaraan jika data_kendaraan kosong
        if (DB::table('data_kendaraan')->count() === 0) {
            $trukTersedia = DB::table('data_aset')->whereNotNull('no_polisi')->where('no_polisi', '!=', '-')->get();
            $urutan = 1;
            foreach ($trukTersedia as $t) {
                $kdKnd = 'KND-' . str_pad($urutan++, 3, '0', STR_PAD_LEFT);
                DB::table('data_kendaraan')->updateOrInsert(
                    ['kode_kendaraan' => $kdKnd],
                    [
                        'kode_aset'        => $t->kode_aset,
                        'no_polisi'        => $t->no_polisi,
                        'no_mesin'         => $t->no_mesin,
                        'no_rangka'        => $t->no_rangka,
                        'merek_kendaraan'  => $t->merek_aset,
                        'jenis_kendaraan'  => $t->jenis_kendaraan ?? 'Colt Diesel Double',
                        'tipe_armada'      => $t->jenis_kendaraan ?? 'Colt Diesel Double',
                        'muatan'           => $t->muatan ?? '200 Zak (8 Ton)',
                        'tahun_pembuatan'  => $t->tahun_pembuatan,
                        'tanggal_kir'      => $t->tanggal_kir,
                        'tanggal_pajak'    => $t->tanggal_pajak,
                        'status_kendaraan' => $t->status_aset ?? 'aktif',
                        'nama_pemilik'     => $t->nama_pemilik ?? 'PT Putra Balkom Jaya',
                        'dibuat_pada'      => now(),
                        'diperbarui_pada'  => now(),
                    ]
                );
            }
        }
    }
}

