<?php

namespace App\Http\Controllers\Operasional\Pengiriman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\SuratJalan;
use App\Models\Operasional\Driver;
use App\Models\Operasional\Kendaraan;
use App\Models\Keuangan\PembelianSO;
use App\Models\Master\Customer;
use App\Models\Operasional\Gudang;
use Carbon\Carbon;

class SuratJalanController extends Controller
{
    /**
     * Tampilkan data daftar pengiriman dan surat jalan distribusi.
     */
    public function index(Request $request)
    {
        // Pastikan master SO & pengiriman memiliki sample data jika kosong
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');

        $query = SuratJalan::with([
            'salesOrder.customer',
            'salesOrder.gudang',
            'driver',
            'kendaraan.jenisAset',
        ]);

        // Filter pencarian multi-kolom
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_surat_jalan', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan', 'like', "%{$kataKunci}%")
                  ->orWhereHas('salesOrder', function ($qSO) use ($kataKunci) {
                      $qSO->where('nomor_so', 'like', "%{$kataKunci}%")
                          ->orWhereHas('customer', function ($qCust) use ($kataKunci) {
                              $qCust->where('nama_toko_bangunan', 'like', "%{$kataKunci}%")
                                    ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%")
                                    ->orWhere('alamat', 'like', "%{$kataKunci}%");
                          });
                  })
                  ->orWhereHas('driver', function ($qDrv) use ($kataKunci) {
                      $qDrv->where('nama_karyawan', 'like', "%{$kataKunci}%")
                           ->orWhere('kode_karyawan', 'like', "%{$kataKunci}%")
                           ->orWhere('no_hp', 'like', "%{$kataKunci}%");
                  })
                  ->orWhereHas('kendaraan', function ($qKnd) use ($kataKunci) {
                      $qKnd->where('no_polisi', 'like', "%{$kataKunci}%")
                           ->orWhere('nama_aset', 'like', "%{$kataKunci}%")
                           ->orWhere('kode_aset', 'like', "%{$kataKunci}%");
                  });
            });
        }

        // Filter status pengiriman
        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $query->where('status_pengiriman', $statusFilter);
        }

        $daftarPengiriman = $query->orderBy('tanggal_kirim', 'desc')->get();

        // 4 KPI Statistik Pengiriman
        $semuaPengiriman = SuratJalan::all();
        $totalPengiriman = $semuaPengiriman->count();
        $pengirimanJalan = $semuaPengiriman->where('status_pengiriman', 'dalam_perjalanan')->count();
        $pengirimanSelesai = $semuaPengiriman->where('status_pengiriman', 'terkirim')->count();
        $pengirimanMenunggu = $semuaPengiriman->whereIn('status_pengiriman', ['menunggu', 'retur'])->count();

        // Master Data untuk Dropdown Modal
        $daftarDriver = Driver::where('kategori_karyawan', 'driver')->orderBy('nama_karyawan', 'asc')->get();
        if ($daftarDriver->isEmpty()) {
            $daftarDriver = Driver::orderBy('nama_karyawan', 'asc')->get();
        }

        $daftarKendaraan = Kendaraan::with('jenisAset')->orderBy('nama_aset', 'asc')->get();
        $daftarSO = PembelianSO::with(['customer', 'gudang'])->orderBy('dibuat_pada', 'desc')->get();

        return view('operasional.pengiriman.surat_jalan', compact(
            'daftarPengiriman',
            'kataKunci',
            'statusFilter',
            'totalPengiriman',
            'pengirimanJalan',
            'pengirimanSelesai',
            'pengirimanMenunggu',
            'daftarDriver',
            'daftarKendaraan',
            'daftarSO'
        ));
    }

    /**
     * Simpan Surat Jalan baru ke database.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'nomor_surat_jalan.required' => 'Nomor surat jalan wajib diisi.',
            'nomor_surat_jalan.unique' => 'Nomor surat jalan sudah terdaftar.',
            'id_so.required' => 'Sales Order (SO) tujuan wajib dipilih.',
            'id_so.exists' => 'Sales Order tidak valid.',
            'kode_driver.required' => 'Driver pengemudi wajib dipilih.',
            'kode_driver.exists' => 'Data driver tidak valid.',
            'kode_aset.required' => 'Armada truk pengiriman wajib dipilih.',
            'kode_aset.exists' => 'Data armada truk tidak valid.',
            'tanggal_kirim.required' => 'Tanggal dan jam keberangkatan wajib diisi.',
            'status_pengiriman.required' => 'Status pengiriman wajib dipilih.',
        ];

        $validated = $request->validate([
            'nomor_surat_jalan' => 'required|string|max:50|unique:pengiriman,nomor_surat_jalan',
            'id_so' => 'required|integer|exists:pembelian_so,id_so',
            'kode_driver' => 'required|string|max:30|exists:data_karyawan,kode_karyawan',
            'kode_aset' => 'required|string|max:30|exists:data_aset,kode_aset',
            'tanggal_kirim' => 'required|date',
            'status_pengiriman' => 'required|in:menunggu,dalam_perjalanan,terkirim,retur',
            'keterangan' => 'nullable|string',
        ], $pesanKustom);

        $suratJalan = SuratJalan::create([
            'nomor_surat_jalan' => strtoupper(trim($validated['nomor_surat_jalan'])),
            'id_so' => $validated['id_so'],
            'kode_driver' => $validated['kode_driver'],
            'kode_aset' => $validated['kode_aset'],
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'status_pengiriman' => $validated['status_pengiriman'],
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
        ]);

        // Perbarui status Sales Order menjadi 'dikirim' jika masih draft/disetujui
        $so = PembelianSO::find($validated['id_so']);
        if ($so && in_array($so->status_so, ['draft', 'disetujui'])) {
            $so->update(['status_so' => 'dikirim', 'diperbarui_pada' => now()]);
        }

        return redirect()->route('operasional.pengiriman.surat_jalan')
            ->with('sukses', "Surat Jalan {$suratJalan->nomor_surat_jalan} berhasil diterbitkan!");
    }

    /**
     * Ambil data detail pengiriman (JSON) untuk modal Alpine.js dan cetak SJ.
     */
    public function ambilDetail($id_pengiriman)
    {
        $pengiriman = SuratJalan::with([
            'salesOrder.customer',
            'salesOrder.gudang',
            'driver',
            'kendaraan.jenisAset',
        ])->find($id_pengiriman);

        if (!$pengiriman) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data surat jalan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $pengiriman
        ]);
    }

    /**
     * Perbarui data Surat Jalan.
     */
    public function perbarui(Request $request, $id_pengiriman)
    {
        $suratJalan = SuratJalan::findOrFail($id_pengiriman);

        $pesanKustom = [
            'id_so.required' => 'Sales Order (SO) tujuan wajib dipilih.',
            'id_so.exists' => 'Sales Order tidak valid.',
            'kode_driver.required' => 'Driver pengemudi wajib dipilih.',
            'kode_driver.exists' => 'Data driver tidak valid.',
            'kode_aset.required' => 'Armada truk pengiriman wajib dipilih.',
            'kode_aset.exists' => 'Data armada truk tidak valid.',
            'tanggal_kirim.required' => 'Tanggal dan jam keberangkatan wajib diisi.',
            'status_pengiriman.required' => 'Status pengiriman wajib dipilih.',
        ];

        $validated = $request->validate([
            'id_so' => 'required|integer|exists:pembelian_so,id_so',
            'kode_driver' => 'required|string|max:30|exists:data_karyawan,kode_karyawan',
            'kode_aset' => 'required|string|max:30|exists:data_aset,kode_aset',
            'tanggal_kirim' => 'required|date',
            'status_pengiriman' => 'required|in:menunggu,dalam_perjalanan,terkirim,retur',
            'keterangan' => 'nullable|string',
        ], $pesanKustom);

        $suratJalan->update([
            'id_so' => $validated['id_so'],
            'kode_driver' => $validated['kode_driver'],
            'kode_aset' => $validated['kode_aset'],
            'tanggal_kirim' => $validated['tanggal_kirim'],
            'status_pengiriman' => $validated['status_pengiriman'],
            'keterangan' => $validated['keterangan'] ? trim($validated['keterangan']) : null,
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('operasional.pengiriman.surat_jalan')
            ->with('sukses', "Data Surat Jalan {$suratJalan->nomor_surat_jalan} berhasil diperbarui!");
    }

    /**
     * Perbarui status pengiriman secara cepat (Quick Status Update).
     */
    public function perbaruiStatus(Request $request, $id_pengiriman)
    {
        $suratJalan = SuratJalan::findOrFail($id_pengiriman);

        $validated = $request->validate([
            'status_pengiriman' => 'required|in:menunggu,dalam_perjalanan,terkirim,retur',
        ]);

        $suratJalan->update([
            'status_pengiriman' => $validated['status_pengiriman'],
            'diperbarui_pada' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'sukses',
                'pesan' => "Status Surat Jalan {$suratJalan->nomor_surat_jalan} diperbarui menjadi {$validated['status_pengiriman']}!"
            ]);
        }

        return redirect()->route('operasional.pengiriman.surat_jalan')
            ->with('sukses', "Status Surat Jalan {$suratJalan->nomor_surat_jalan} diperbarui!");
    }

    /**
     * Hapus data Surat Jalan dari database.
     */
    public function hapus($id_pengiriman)
    {
        $suratJalan = SuratJalan::findOrFail($id_pengiriman);
        $nomorSJ = $suratJalan->nomor_surat_jalan;

        try {
            $suratJalan->delete();

            return redirect()->route('operasional.pengiriman.surat_jalan')
                ->with('sukses', "Surat Jalan {$nomorSJ} berhasil dihapus dari sistem!");
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('operasional.pengiriman.surat_jalan')
                ->with('error', "Gagal menghapus Surat Jalan {$nomorSJ}! Data telah memiliki Berita Acara Penerimaan (Rilisan).");
        }
    }

    /**
     * Generator kode otomatis untuk Nomor Surat Jalan.
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');
        $formatTanggal = date('Ymd');

        if ($mode === 'acak') {
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 3; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'SJ-' . $formatTanggal . '-' . $acak;
                $sudahAda = DB::table('pengiriman')->where('nomor_surat_jalan', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('SJ-' . $formatTanggal . '-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Tanggal & Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarSJ = DB::table('pengiriman')
            ->where('nomor_surat_jalan', 'like', 'SJ-%')
            ->pluck('nomor_surat_jalan');

        $nomorTerpakai = [];
        foreach ($daftarSJ as $kode) {
            if (preg_match('/SJ-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'SJ-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan master data Customer, Gudang, Sales Order, dan Pengiriman awal tersedia sesuai skema tabel MySQL.
     */
    private function pastikanDataAwalTersedia(): void
    {
        // 1. Pastikan Wilayah ada
        $wilayahPertama = DB::table('data_wilayah')->value('kode_wilayah');
        if (!$wilayahPertama) {
            DB::table('data_wilayah')->insert([
                'kode_wilayah' => 'WLY-001',
                'nama_wilayah' => 'Wilayah Jakarta & Sekitarnya',
                'dibuat_pada' => now(),
                'diperbarui_pada' => now(),
            ]);
            $wilayahPertama = 'WLY-001';
        }

        // 2. Pastikan Customer ada
        $jumlahCustomer = DB::table('data_customer')->count();
        if ($jumlahCustomer === 0) {
            DB::table('data_customer')->insert([
                [
                    'kode_customer' => 'CUST-001',
                    'kode_wilayah' => $wilayahPertama,
                    'nama_toko_bangunan' => 'TB Maju Jaya Sentosa',
                    'nama_pemilik' => 'H. Hendra Wijaya',
                    'alamat' => 'Jl. Raya Karawang Barat No. 88, Karawang',
                    'no_hp' => '081298765432',
                    'no_ktp' => '3215012304850001',
                    'foto_ktp' => null,
                    'plafon_piutang' => 50000000.00,
                    'saldo_piutang' => 15000000.00,
                    'saldo_deposit' => 0.00,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ],
                [
                    'kode_customer' => 'CUST-002',
                    'kode_wilayah' => $wilayahPertama,
                    'nama_toko_bangunan' => 'TB Berkah Bangunan',
                    'nama_pemilik' => 'Hj. Siti Aminah',
                    'alamat' => 'Jl. Lingkar Luar No. 12, Bekasi',
                    'no_hp' => '081387654321',
                    'no_ktp' => '3216014408870002',
                    'foto_ktp' => null,
                    'plafon_piutang' => 30000000.00,
                    'saldo_piutang' => 0.00,
                    'saldo_deposit' => 5000000.00,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ],
            ]);
        }

        // 3. Pastikan Gudang ada
        $gudangPertama = DB::table('list_gudang_so')->value('kode_gudang');
        if (!$gudangPertama) {
            $kodeBarangSemen = DB::table('data_semen')->value('kode_barang') ?? 'SMN-002';

            DB::table('list_gudang_so')->insert([
                [
                    'kode_gudang' => 'GDG-PBJ1',
                    'nama_gudang' => 'Gudang Utama Pabrik PBJ',
                    'jenis_gudang' => 'Utama',
                    'kode_barang' => $kodeBarangSemen,
                    'plant' => 'Plant Cikarang',
                    'harga_barang' => 62000.00,
                    'stok_tersedia' => 50000,
                    'distrik' => 'Bekasi',
                    'sub_distrik' => 'Cikarang',
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ],
            ]);
            $gudangPertama = 'GDG-PBJ1';
        }

        // 4. Pastikan Sales Order ada
        $jumlahSO = DB::table('pembelian_so')->count();
        if ($jumlahSO === 0) {
            $customerPertama = DB::table('data_customer')->value('kode_customer') ?? 'CUST-001';

            DB::table('pembelian_so')->insert([
                [
                    'id_so' => 1,
                    'nomor_so' => 'SO-20260901-001',
                    'tanggal_so' => '2026-09-01',
                    'kode_customer' => $customerPertama,
                    'kode_gudang' => $gudangPertama,
                    'jumlah_zak' => 500,
                    'harga_satuan' => 62000.00,
                    'total_harga' => 31000000.00,
                    'status_so' => 'dikirim',
                    'dibuat_oleh' => 'Staff Sales PBJ',
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ],
                [
                    'id_so' => 2,
                    'nomor_so' => 'SO-20260902-002',
                    'tanggal_so' => '2026-09-02',
                    'kode_customer' => $customerPertama,
                    'kode_gudang' => $gudangPertama,
                    'jumlah_zak' => 300,
                    'harga_satuan' => 62000.00,
                    'total_harga' => 18600000.00,
                    'status_so' => 'diproses',
                    'dibuat_oleh' => 'Staff Sales PBJ',
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ],
            ]);
        }

        // 5. Pastikan Pengiriman awal ada
        $jumlahPengiriman = DB::table('pengiriman')->count();
        if ($jumlahPengiriman === 0) {
            $driverSatu = DB::table('data_karyawan')->where('kategori_karyawan', 'driver')->value('kode_karyawan') 
                          ?? (DB::table('data_karyawan')->value('kode_karyawan') ?? 'DRV-001');
            $asetSatu = DB::table('data_aset')->value('kode_aset') ?? 'TRK-001';
            $soSatu = DB::table('pembelian_so')->value('id_so') ?? 1;

            DB::table('pengiriman')->insert([
                [
                    'id_pengiriman' => 1,
                    'nomor_surat_jalan' => 'SJ-001',
                    'id_so' => $soSatu,
                    'kode_aset' => $asetSatu,
                    'kode_driver' => $driverSatu,
                    'tanggal_kirim' => Carbon::now()->subHours(3),
                    'status_pengiriman' => 'dalam_perjalanan',
                    'keterangan' => 'Pengiriman 500 Zak Semen PCC 50Kg ke TB Maju Jaya Sentosa (Segel No: PBJ-9921)',
                    'dibuat_pada' => Carbon::now()->subHours(3),
                    'diperbarui_pada' => Carbon::now()->subHours(1),
                ],
            ]);
        }
    }
}
