<?php

namespace App\Http\Controllers\Keuangan\Akuntansi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Keuangan\AsetPerusahaan;
use App\Models\Keuangan\RiwayatPenyusutan;
use App\Models\Operasional\DataKendaraan;
use App\Helpers\GeneratorKodeOtomatis;
use Carbon\Carbon;

class AsetPerusahaanController extends Controller
{
    /**
     * Tampilkan daftar aktiva tetap perusahaan dan kalkulasi nilai buku & depresiasi.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $filterJenis = $request->input('jenis');

        $query = AsetPerusahaan::with(['jenisAset', 'dataKendaraan', 'riwayatPenyusutan']);

        if ($filterJenis) {
            $query->where('kode_jenis_aset', $filterJenis);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('no_polisi', 'like', "%{$kataKunci}%");
            });
        }

        $daftarAset = $query->orderBy('kode_aset', 'asc')->get();
        $daftarJenis = DB::table('data_jenis_aset')->orderBy('jenis_aset')->get();

        // 5 Indikator Finansial Aset
        $totalNilaiPerolehan = AsetPerusahaan::sum('harga_perolehan');
        $totalAkumulasiSusut = AsetPerusahaan::sum('akumulasi_penyusutan');
        $totalNilaiBuku = AsetPerusahaan::sum('nilai_buku');
        $totalUnitAset = AsetPerusahaan::count();

        // Estimasi beban penyusutan bulan ini
        $estimasiSusutBulanIni = 0;
        foreach ($daftarAset as $aset) {
            /** @var \App\Models\Keuangan\AsetPerusahaan $aset */
            $estimasiSusutBulanIni += $aset->hitungPenyusutanBulanan();
        }

        // Generator kode aset otomatis
        $kodeOtomatis = GeneratorKodeOtomatis::buatKode('data_aset', 'kode_aset', 'AST-', 3);

        // 10 Riwayat Penyusutan Terakhir
        $riwayatTerbaru = RiwayatPenyusutan::with('aset')
            ->orderBy('tanggal_penyusutan', 'desc')
            ->orderBy('id_penyusutan', 'desc')
            ->limit(10)
            ->get();

        return view('keuangan.akuntansi.aset_perusahaan', compact(
            'daftarAset',
            'daftarJenis',
            'kataKunci',
            'filterJenis',
            'totalNilaiPerolehan',
            'totalAkumulasiSusut',
            'totalNilaiBuku',
            'totalUnitAset',
            'estimasiSusutBulanIni',
            'kodeOtomatis',
            'riwayatTerbaru'
        ));
    }

    /**
     * Simpan aset tetap baru dengan parameter akuntansi dan penyusutan.
     */
    public function store(Request $request)
    {
        $jumlahUnit = max(1, min(100, (int) ($request->jumlah_unit ?? 1)));

        $request->validate([
            'kode_aset'         => 'nullable|string|max:30',
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'tanggal_pembelian' => 'required|date',
            'harga_perolehan'   => 'required|numeric|min:0',
            'jumlah_unit'       => 'nullable|integer|min:1|max:100',
            'nilai_residu'      => 'nullable|numeric|min:0',
            'metode_penyusutan' => 'required|in:Tidak Disusutkan,Garis Lurus,Saldo Menurun',
            'umur_manfaat'      => 'nullable|integer|min:0',
            'tarif_penyusutan'  => 'nullable|numeric|min:0',
        ]);

        $hargaSatuan = (float) $request->harga_perolehan;
        $nilaiResidu = (float) ($request->nilai_residu ?? 0);
        $jenisAset   = $request->kode_jenis_aset;

        // Default Atribut Akuntansi & COA Berdasarkan Golongan Aset
        $umurManfaat = (int) ($request->umur_manfaat ?? 0);
        $tarifSusut  = (float) ($request->tarif_penyusutan ?? 0);
        $metodeSusut = $request->metode_penyusutan;

        $kodeAkunAset = '1206';
        $kodeAkunAkum = '1207';
        $kodeAkunBeban= '6108';

        if ($jenisAset === 'AST-TNH' || $jenisAset === 'AST-BDG') {
            // Tanah & Bangunan Properti (Jika ada bangunan disusutkan, jika murni tanah tidak disusutkan)
            $jenisAset = 'AST-TNH';
            if ($umurManfaat > 0) {
                $umurManfaat = $umurManfaat ?: 20;
                $tarifSusut  = $tarifSusut ?: 5.00;
                $metodeSusut = 'Garis Lurus';
                $kodeAkunAset = '1204'; // Bangunan & Gedung
                $kodeAkunAkum = '1205';
                $kodeAkunBeban= '6106';
            } else {
                $umurManfaat = 0;
                $tarifSusut  = 0.00;
                $metodeSusut = 'Tidak Disusutkan';
                $kodeAkunAset = '1200'; // Tanah & Lahan
                $kodeAkunAkum = null;
                $kodeAkunBeban= null;
            }
        } elseif ($jenisAset === 'AST-TRK') {
            // Armada Truk & Tronton: 8 Tahun, Garis Lurus 12.5%
            $umurManfaat = $umurManfaat ?: 8;
            $tarifSusut  = $tarifSusut ?: 12.50;
            $metodeSusut = 'Garis Lurus';
            $kodeAkunAset = '1201';
            $kodeAkunAkum = '1202';
            $kodeAkunBeban= '6105';
        } elseif ($jenisAset === 'AST-GDG') {
            // Mesin & Fasilitas Gudang: 8 Tahun, Garis Lurus 12.5%
            $umurManfaat = $umurManfaat ?: 8;
            $tarifSusut  = $tarifSusut ?: 12.50;
            $metodeSusut = 'Garis Lurus';
            $kodeAkunAset = '1203';
            $kodeAkunAkum = '1208';
            $kodeAkunBeban= '6107';
        } else {
            // Peralatan & Inventaris Kantor: 4 Tahun, Garis Lurus 25%
            $umurManfaat = $umurManfaat ?: 4;
            $tarifSusut  = $tarifSusut ?: 25.00;
            $metodeSusut = 'Garis Lurus';
            $kodeAkunAset = '1206';
            $kodeAkunAkum = '1207';
            $kodeAkunBeban= '6108';
        }

        $namaPemilik = $request->filled('nama_pemilik') ? trim($request->nama_pemilik) : 'PT Putra Balkom Jaya';
        $statusAset  = $request->filled('status_aset') ? $request->status_aset : 'aktif';
        $jenisKendaraan = $request->input('jenis_kendaraan');
        $merekAset   = $request->input('merek_aset');
        $muatan      = $request->input('muatan');
        $tahunPembuatan = $request->filled('tahun_pembuatan') ? (int) $request->tahun_pembuatan : null;
        $tanggalKir  = $request->input('tanggal_kir') ?: null;
        $tanggalPajak= $request->input('tanggal_pajak') ?: null;
        $keterangan  = $request->input('keterangan');

        DB::beginTransaction();
        try {
            // Siapkan daftar kode aset unik sekuensial
            if ($jumlahUnit === 1 && $request->filled('kode_aset')) {
                $daftarKodeAset = [strtoupper(trim($request->kode_aset))];
            } else {
                $daftarKodeAset = GeneratorKodeOtomatis::buatBanyakKode('data_aset', 'kode_aset', 'AST-', $jumlahUnit, 3);
            }

            // Jika armada truk, siapkan daftar kode kendaraan unik
            $daftarKodeKendaraan = [];
            if ($jenisAset === 'AST-TRK') {
                $daftarKodeKendaraan = GeneratorKodeOtomatis::buatBanyakKode('data_kendaraan', 'kode_kendaraan', 'KND-', $jumlahUnit, 3);
            }

            $rincianUnitInput = $request->input('rincian_unit', []);

            for ($i = 0; $i < $jumlahUnit; $i++) {
                $nomorUrut = $i + 1;
                $kodeAsetSekarang = $daftarKodeAset[$i];
                $namaAsetUnit = $jumlahUnit > 1 ? ($request->nama_aset . ' #' . str_pad($nomorUrut, 2, '0', STR_PAD_LEFT)) : $request->nama_aset;

                // Tentukan data kendaraan jika truk
                $platNomorUnit = null;
                $noMesinUnit = '-';
                $noRangkaUnit = '-';

                if ($jenisAset === 'AST-TRK') {
                    $rincianSekarang = $rincianUnitInput[$i] ?? [];
                    $platNomorUnit = !empty($rincianSekarang['no_polisi']) 
                        ? strtoupper(trim($rincianSekarang['no_polisi'])) 
                        : ($jumlahUnit === 1 && $request->filled('no_polisi') 
                            ? strtoupper(trim($request->no_polisi)) 
                            : ('B ' . (9000 + $i) . ' PBJ'));
                    $noMesinUnit = !empty($rincianSekarang['no_mesin']) 
                        ? strtoupper(trim($rincianSekarang['no_mesin'])) 
                        : ($request->filled('no_mesin') ? strtoupper(trim($request->no_mesin)) : '-');
                    $noRangkaUnit = !empty($rincianSekarang['no_rangka']) 
                        ? strtoupper(trim($rincianSekarang['no_rangka'])) 
                        : ($request->filled('no_rangka') ? strtoupper(trim($request->no_rangka)) : '-');
                }

                // 1. Simpan ke Master Aset Tetap Akuntansi
                AsetPerusahaan::create([
                    'kode_aset'            => $kodeAsetSekarang,
                    'kode_jenis_aset'      => $jenisAset,
                    'nama_aset'            => $namaAsetUnit,
                    'tanggal_pembelian'    => $request->tanggal_pembelian,
                    'harga_aset'           => $hargaSatuan,
                    'harga_perolehan'      => $hargaSatuan,
                    'nilai_residu'         => $nilaiResidu,
                    'umur_manfaat'         => $umurManfaat,
                    'metode_penyusutan'    => $metodeSusut,
                    'tarif_penyusutan'     => $tarifSusut,
                    'kode_akun_aset'       => (!empty($kodeAkunAset) && DB::table('data_kode_akun')->where('kode_akun', $kodeAkunAset)->exists()) ? $kodeAkunAset : null,
                    'kode_akun_akumulasi'  => (!empty($kodeAkunAkum) && DB::table('data_kode_akun')->where('kode_akun', $kodeAkunAkum)->exists()) ? $kodeAkunAkum : null,
                    'kode_akun_beban'      => (!empty($kodeAkunBeban) && DB::table('data_kode_akun')->where('kode_akun', $kodeAkunBeban)->exists()) ? $kodeAkunBeban : null,
                    'akumulasi_penyusutan' => 0.00,
                    'nilai_buku'           => $hargaSatuan,
                    'status_aset'          => $statusAset,
                    'nama_pemilik'         => $namaPemilik,
                    'no_polisi'            => $platNomorUnit ?? '-',
                    'no_mesin'             => $noMesinUnit,
                    'no_rangka'            => $noRangkaUnit,
                    'merek_aset'           => $merekAset ?? '-',
                    'jenis_kendaraan'      => $jenisKendaraan ?? '-',
                    'muatan'               => $muatan ?? '-',
                    'tahun_pembuatan'      => $tahunPembuatan,
                    'tanggal_kir'          => $tanggalKir,
                    'tanggal_pajak'        => $tanggalPajak,
                    'keterangan'           => $keterangan,
                ]);

                // 2. Relasi Otomatis Armada Truk ke data_kendaraan
                if ($jenisAset === 'AST-TRK') {
                    $kodeKndSekarang = $daftarKodeKendaraan[$i];

                    DataKendaraan::create([
                        'kode_kendaraan'   => $kodeKndSekarang,
                        'kode_aset'        => $kodeAsetSekarang,
                        'no_polisi'        => $platNomorUnit,
                        'no_mesin'         => $noMesinUnit !== '-' ? $noMesinUnit : null,
                        'no_rangka'        => $noRangkaUnit !== '-' ? $noRangkaUnit : null,
                        'merek_kendaraan'  => $merekAset ?? 'Hino',
                        'jenis_kendaraan'  => $jenisKendaraan ?? 'Tronton Wingbox',
                        'tipe_armada'      => $jenisKendaraan ?? 'Tronton Wingbox',
                        'muatan'           => $muatan ?? '25 Ton',
                        'tahun_pembuatan'  => $tahunPembuatan,
                        'tanggal_kir'      => $tanggalKir,
                        'tanggal_pajak'    => $tanggalPajak,
                        'status_kendaraan' => $statusAset,
                        'nama_pemilik'     => $namaPemilik,
                    ]);
                }
            }

            DB::commit();

            if ($jumlahUnit > 1) {
                $pesanSukses = "Berhasil mendaftarkan {$jumlahUnit} unit aset [{$request->nama_aset}] sekaligus (Kode: {$daftarKodeAset[0]} s/d {$daftarKodeAset[$jumlahUnit-1]}).";
            } else {
                $pesanSukses = "Aset [{$request->nama_aset}] ({$daftarKodeAset[0]}) berhasil didaftarkan.";
            }

            if ($jenisAset === 'AST-TRK') {
                $pesanSukses .= " Unit armada baru otomatis dicatat di Master Kendaraan Operasional.";
            }

            return redirect()->route('keuangan.akuntansi.aset')->with('sukses', $pesanSukses);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan aset: ' . $e->getMessage());
        }
    }

    /**
     * Proses eksekusi penyusutan bulanan untuk seluruh aktiva tetap yang aktif.
     */
    public function prosesPenyusutanBulanan(Request $request)
    {
        $bulan = (int) ($request->input('periode_bulan') ?? now()->month);
        $tahun = (int) ($request->input('periode_tahun') ?? now()->year);
        $tanggalPenyusutan = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');

        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $namaBulanList[$bulan] ?? 'Bulan ' . $bulan;

        // Periksa apakah periode ini sudah pernah disusutkan
        $sudahAda = RiwayatPenyusutan::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->exists();

        if ($sudahAda && !$request->has('paksa')) {
            return redirect()->back()->with('error', "Penyusutan untuk periode {$namaBulan} {$tahun} sudah pernah diproses sebelumnya.");
        }

        DB::beginTransaction();
        try {
            $daftarAset = AsetPerusahaan::where('status_aset', 'aktif')
                ->where('metode_penyusutan', '!=', 'Tidak Disusutkan')
                ->where('nilai_buku', '>', 0)
                ->get();

            $totalAsetDisusutkan = 0;
            $totalNominalSusut   = 0.00;

            foreach ($daftarAset as $aset) {
                /** @var \App\Models\Keuangan\AsetPerusahaan $aset */
                $nominalSusut = $aset->hitungPenyusutanBulanan();

                if ($nominalSusut > 0) {
                    $nomorPenyusutan = GeneratorKodeOtomatis::buatKodeTransaksi('riwayat_penyusutan', 'nomor_penyusutan', 'DEP-AST-', $tanggalPenyusutan);
                    $nomorJurnal     = GeneratorKodeOtomatis::buatKodeTransaksi('jurnal_umum', 'nomor_jurnal', 'JU-', $tanggalPenyusutan);

                    $akumulasiBaru = (float) $aset->akumulasi_penyusutan + $nominalSusut;
                    $nilaiBukuBaru = max(0.00, (float) $aset->nilai_buku - $nominalSusut);

                    // 1. Catat ke Jurnal Umum Double-Entry (Debit Beban, Kredit Akumulasi)
                    if ($aset->kode_akun_beban && $aset->kode_akun_akumulasi) {
                        // Debit Beban Penyusutan
                        DB::table('jurnal_umum')->insert([
                            'nomor_jurnal'        => $nomorJurnal,
                            'tanggal_transaksi'   => $tanggalPenyusutan,
                            'kode_akun'           => $aset->kode_akun_beban,
                            'posisi'              => 'Debit',
                            'nominal'             => $nominalSusut,
                            'keterangan'          => "Beban Penyusutan {$aset->nama_aset} ({$aset->kode_aset}) - {$namaBulan} {$tahun}",
                            'referensi_transaksi' => $nomorPenyusutan,
                            'dibuat_oleh'         => 'spv_keuangan',
                            'dibuat_pada'         => now(),
                        ]);

                        // Kredit Akumulasi Penyusutan
                        DB::table('jurnal_umum')->insert([
                            'nomor_jurnal'        => $nomorJurnal,
                            'tanggal_transaksi'   => $tanggalPenyusutan,
                            'kode_akun'           => $aset->kode_akun_akumulasi,
                            'posisi'              => 'Kredit',
                            'nominal'             => $nominalSusut,
                            'keterangan'          => "Akumulasi Penyusutan {$aset->nama_aset} ({$aset->kode_aset}) - {$namaBulan} {$tahun}",
                            'referensi_transaksi' => $nomorPenyusutan,
                            'dibuat_oleh'         => 'spv_keuangan',
                            'dibuat_pada'         => now(),
                        ]);

                        // Update saldo berjalan COA
                        DB::table('data_kode_akun')->where('kode_akun', $aset->kode_akun_beban)->increment('saldo_berjalan', $nominalSusut);
                        DB::table('data_kode_akun')->where('kode_akun', $aset->kode_akun_akumulasi)->increment('saldo_berjalan', $nominalSusut);
                    }

                    // 2. Simpan Riwayat Penyusutan Aset
                    RiwayatPenyusutan::create([
                        'nomor_penyusutan'     => $nomorPenyusutan,
                        'kode_aset'            => $aset->kode_aset,
                        'tanggal_penyusutan'   => $tanggalPenyusutan,
                        'periode_bulan'        => $bulan,
                        'periode_tahun'        => $tahun,
                        'beban_penyusutan'     => $nominalSusut,
                        'akumulasi_penyusutan' => $akumulasiBaru,
                        'nilai_buku'           => $nilaiBukuBaru,
                        'nomor_jurnal'         => $nomorJurnal,
                        'keterangan'           => "Penyusutan Rutin Periode {$namaBulan} {$tahun}",
                        'dibuat_oleh'          => 'spv_keuangan',
                    ]);

                    // 3. Perbarui Nilai Buku dan Akumulasi pada Master Aset
                    $aset->update([
                        'akumulasi_penyusutan' => $akumulasiBaru,
                        'nilai_buku'           => $nilaiBukuBaru,
                    ]);

                    $totalAsetDisusutkan++;
                    $totalNominalSusut += $nominalSusut;
                }
            }

            DB::commit();

            $nominalRupiah = 'Rp ' . number_format($totalNominalSusut, 0, ',', '.');
            return redirect()->route('keuangan.akuntansi.aset')->with('sukses', "Tutup buku penyusutan periode {$namaBulan} {$tahun} berhasil diproses untuk {$totalAsetDisusutkan} unit aset dengan total beban {$nominalRupiah}. Ayat jurnal telah diposting otomatis ke Buku Besar.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses penyusutan bulanan: ' . $e->getMessage());
        }
    }

    /**
     * Ambil detail data aset untuk modal Alpine.js.
     */
    public function show($kode_aset)
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

        return response()->json([
            'status' => 'sukses',
            'data' => $aset
        ]);
    }

    /**
     * Perbarui data aset perusahaan.
     */
    public function update(Request $request, $kode_aset)
    {
        $request->validate([
            'kode_jenis_aset'   => 'required|string|exists:data_jenis_aset,kode_jenis_aset',
            'nama_aset'         => 'required|string|max:100',
            'tanggal_pembelian' => 'required|date',
            'harga_aset'        => 'required|numeric|min:0',
        ]);

        $updateData = [
            'kode_jenis_aset'   => $request->kode_jenis_aset,
            'nama_aset'         => trim($request->nama_aset),
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'harga_aset'        => $request->harga_aset,
            'no_polisi'         => !empty($request->no_polisi) ? strtoupper(trim($request->no_polisi)) : '-',
            'status_aset'       => $request->status_aset ?? 'aktif',
            'nama_pemilik'      => $request->filled('nama_pemilik') ? trim($request->nama_pemilik) : 'PT Putra Balkom Jaya',
            'merek_aset'        => $request->input('merek_aset'),
            'jenis_kendaraan'   => $request->input('jenis_kendaraan'),
            'muatan'            => $request->input('muatan'),
            'no_mesin'          => $request->input('no_mesin'),
            'no_rangka'         => $request->input('no_rangka'),
            'tahun_pembuatan'   => $request->filled('tahun_pembuatan') ? (int) $request->tahun_pembuatan : null,
            'tanggal_kir'       => $request->input('tanggal_kir') ?: null,
            'tanggal_pajak'     => $request->input('tanggal_pajak') ?: null,
            'keterangan'        => $request->input('keterangan'),
            'diperbarui_pada'   => now(),
        ];

        DB::table('data_aset')->where('kode_aset', $kode_aset)->update($updateData);

        // Jika bertipe truk atau memiliki record kendaraan, sinkronkan ke data_kendaraan
        if ($request->kode_jenis_aset === 'AST-TRK' || DB::table('data_kendaraan')->where('kode_aset', $kode_aset)->exists()) {
            DB::table('data_kendaraan')->where('kode_aset', $kode_aset)->update([
                'no_polisi'        => !empty($request->no_polisi) ? strtoupper(trim($request->no_polisi)) : null,
                'merek_kendaraan'  => $request->input('merek_aset') ?? 'Hino',
                'jenis_kendaraan'  => $request->input('jenis_kendaraan') ?? 'Tronton Wingbox',
                'tipe_armada'      => $request->input('jenis_kendaraan') ?? 'Tronton Wingbox',
                'muatan'           => $request->input('muatan'),
                'no_mesin'         => $request->input('no_mesin'),
                'no_rangka'        => $request->input('no_rangka'),
                'tahun_pembuatan'  => $request->filled('tahun_pembuatan') ? (int) $request->tahun_pembuatan : null,
                'tanggal_kir'      => $request->input('tanggal_kir') ?: null,
                'tanggal_pajak'    => $request->input('tanggal_pajak') ?: null,
                'status_kendaraan' => $request->status_aset ?? 'aktif',
                'nama_pemilik'     => $request->filled('nama_pemilik') ? trim($request->nama_pemilik) : 'PT Putra Balkom Jaya',
                'diperbarui_pada'  => now(),
            ]);
        }

        return redirect()->route('keuangan.akuntansi.aset')->with('sukses', "Data aset {$request->nama_aset} ({$kode_aset}) berhasil diperbarui.");
    }

    /**
     * Hapus data aset perusahaan beserta riwayat dan sinkronisasi kendaraan.
     */
    public function destroy($kode_aset)
    {
        $aset = DB::table('data_aset')->where('kode_aset', $kode_aset)->first();
        if (!$aset) {
            return redirect()->route('keuangan.akuntansi.aset')->with('error', 'Data aset tidak ditemukan.');
        }

        DB::beginTransaction();
        try {
            // 1. Hapus riwayat penyusutan aset jika ada
            DB::table('riwayat_penyusutan')->where('kode_aset', $kode_aset)->delete();

            // 2. Periksa apakah terikat ke data_kendaraan
            $kendaraan = DB::table('data_kendaraan')->where('kode_aset', $kode_aset)->first();
            if ($kendaraan) {
                // Periksa apakah kendaraan terikat ke surat jalan / pengiriman atau perbaikan bengkel
                $adaKirim = DB::table('pengiriman')->where('kode_kendaraan', $kendaraan->kode_kendaraan)->exists();
                $adaSpk   = DB::table('perbaikan_kendaraan')->where('kode_kendaraan', $kendaraan->kode_kendaraan)->exists();
                
                if ($adaKirim || $adaSpk) {
                    // Lepaskan keterikatan aset agar riwayat operasional tetap utuh
                    DB::table('data_kendaraan')->where('kode_aset', $kode_aset)->update(['kode_aset' => null]);
                } else {
                    // Jika belum ada transaksi operasional, hapus unit kendaraan fisik
                    DB::table('data_kendaraan')->where('kode_aset', $kode_aset)->delete();
                }
            }

            // 3. Hapus data aset dari master data_aset
            DB::table('data_aset')->where('kode_aset', $kode_aset)->delete();

            DB::commit();

            return redirect()->route('keuangan.akuntansi.aset')->with('sukses', "Aset {$aset->nama_aset} ({$kode_aset}) berhasil dihapus dari inventaris.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('keuangan.akuntansi.aset')->with('error', 'Gagal menghapus aset: ' . $e->getMessage());
        }
    }
}
