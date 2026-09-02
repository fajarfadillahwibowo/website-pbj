<?php

namespace App\Http\Controllers\Operasional\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\StockOpname;
use App\Models\Operasional\Gudang;
use Carbon\Carbon;

class StockOpnameController extends Controller
{
    /**
     * Tampilkan data Stock Opname Fisik Gudang.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');

        $query = StockOpname::with('gudang.barang');

        // Filter pencarian multi-kolom
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_opname', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_gudang', 'like', "%{$kataKunci}%")
                  ->orWhere('petugas_opname', 'like', "%{$kataKunci}%")
                  ->orWhere('keterangan_selisih', 'like', "%{$kataKunci}%")
                  ->orWhereHas('gudang', function ($qG) use ($kataKunci) {
                      $qG->where('nama_gudang', 'like', "%{$kataKunci}%")
                         ->orWhere('plant', 'like', "%{$kataKunci}%")
                         ->orWhere('distrik', 'like', "%{$kataKunci}%");
                  });
            });
        }

        // Filter status konfirmasi
        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $query->where('status_konfirmasi', $statusFilter);
        }

        $daftarOpname = $query->orderBy('tanggal_opname', 'desc')->orderBy('dibuat_pada', 'desc')->get();

        // 4 KPI Statistik Opname Gudang
        $semuaOpname = StockOpname::all();
        $totalOpname = $semuaOpname->count();
        $opnameDikonfirmasi = $semuaOpname->where('status_konfirmasi', 'dikonfirmasi_spv')->count();
        $opnameDraft = $semuaOpname->where('status_konfirmasi', 'draft')->count();
        $totalSelisihFisik = $semuaOpname->sum('selisih');

        // Daftar Fasilitas Gudang untuk Dropdown
        $daftarGudang = Gudang::with('barang')->orderBy('nama_gudang', 'asc')->get();

        return view('operasional.gudang.opname', compact(
            'daftarOpname',
            'kataKunci',
            'statusFilter',
            'totalOpname',
            'opnameDikonfirmasi',
            'opnameDraft',
            'totalSelisihFisik',
            'daftarGudang'
        ));
    }

    /**
     * Simpan data Stock Opname baru ke database.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'nomor_opname.required' => 'Nomor opname wajib diisi.',
            'nomor_opname.unique' => 'Nomor opname sudah terdaftar.',
            'kode_gudang.required' => 'Fasilitas gudang wajib dipilih.',
            'kode_gudang.exists' => 'Gudang tidak valid.',
            'tanggal_opname.required' => 'Tanggal opname fisik wajib diisi.',
            'stok_sistem.required' => 'Kuantitas stok sistem wajib diisi.',
            'stok_fisik.required' => 'Kuantitas stok fisik riil wajib diisi.',
            'status_konfirmasi.required' => 'Status konfirmasi wajib dipilih.',
            'petugas_opname.required' => 'Nama petugas opname wajib diisi.',
        ];

        $validated = $request->validate([
            'nomor_opname' => 'required|string|max:50|unique:opname_gudang,nomor_opname',
            'kode_gudang' => 'required|string|max:30|exists:list_gudang_so,kode_gudang',
            'tanggal_opname' => 'required|date',
            'stok_sistem' => 'required|integer|min:0',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan_selisih' => 'nullable|string',
            'status_konfirmasi' => 'required|in:draft,dikonfirmasi_spv',
            'petugas_opname' => 'required|string|max:50',
        ], $pesanKustom);

        $selisih = (int) $validated['stok_fisik'] - (int) $validated['stok_sistem'];

        $opname = StockOpname::create([
            'nomor_opname' => strtoupper(trim($validated['nomor_opname'])),
            'kode_gudang' => $validated['kode_gudang'],
            'tanggal_opname' => $validated['tanggal_opname'],
            'stok_sistem' => $validated['stok_sistem'],
            'stok_fisik' => $validated['stok_fisik'],
            'selisih' => $selisih,
            'keterangan_selisih' => $validated['keterangan_selisih'] ? trim($validated['keterangan_selisih']) : 'Hasil perhitungan fisik gudang',
            'status_konfirmasi' => $validated['status_konfirmasi'],
            'petugas_opname' => trim($validated['petugas_opname']),
        ]);

        // Jika SPV langsung konfirmasi, sinkronkan kuantitas stok di tabel list_gudang_so
        if ($validated['status_konfirmasi'] === 'dikonfirmasi_spv') {
            Gudang::where('kode_gudang', $validated['kode_gudang'])
                ->update(['stok_tersedia' => $validated['stok_fisik'], 'diperbarui_pada' => now()]);
        }

        return redirect()->route('operasional.gudang.opname')
            ->with('sukses', "Catatan Stock Opname {$opname->nomor_opname} berhasil disimpan!");
    }

    /**
     * Ambil detail data opname (JSON) untuk modal edit dan preview.
     */
    public function ambilDetail($id_opname)
    {
        $opname = StockOpname::with('gudang.barang')->find($id_opname);

        if (!$opname) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data stock opname tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $opname
        ]);
    }

    /**
     * Perbarui data Stock Opname.
     */
    public function perbarui(Request $request, $id_opname)
    {
        $opname = StockOpname::findOrFail($id_opname);

        $pesanKustom = [
            'kode_gudang.required' => 'Fasilitas gudang wajib dipilih.',
            'kode_gudang.exists' => 'Gudang tidak valid.',
            'tanggal_opname.required' => 'Tanggal opname fisik wajib diisi.',
            'stok_sistem.required' => 'Kuantitas stok sistem wajib diisi.',
            'stok_fisik.required' => 'Kuantitas stok fisik wajib diisi.',
            'status_konfirmasi.required' => 'Status konfirmasi wajib dipilih.',
            'petugas_opname.required' => 'Nama petugas opname wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_gudang' => 'required|string|max:30|exists:list_gudang_so,kode_gudang',
            'tanggal_opname' => 'required|date',
            'stok_sistem' => 'required|integer|min:0',
            'stok_fisik' => 'required|integer|min:0',
            'keterangan_selisih' => 'nullable|string',
            'status_konfirmasi' => 'required|in:draft,dikonfirmasi_spv',
            'petugas_opname' => 'required|string|max:50',
        ], $pesanKustom);

        $selisih = (int) $validated['stok_fisik'] - (int) $validated['stok_sistem'];

        $opname->update([
            'kode_gudang' => $validated['kode_gudang'],
            'tanggal_opname' => $validated['tanggal_opname'],
            'stok_sistem' => $validated['stok_sistem'],
            'stok_fisik' => $validated['stok_fisik'],
            'selisih' => $selisih,
            'keterangan_selisih' => $validated['keterangan_selisih'] ? trim($validated['keterangan_selisih']) : null,
            'status_konfirmasi' => $validated['status_konfirmasi'],
            'petugas_opname' => trim($validated['petugas_opname']),
        ]);

        if ($validated['status_konfirmasi'] === 'dikonfirmasi_spv') {
            Gudang::where('kode_gudang', $validated['kode_gudang'])
                ->update(['stok_tersedia' => $validated['stok_fisik'], 'diperbarui_pada' => now()]);
        }

        return redirect()->route('operasional.gudang.opname')
            ->with('sukses', "Data Stock Opname {$opname->nomor_opname} berhasil diperbarui!");
    }

    /**
     * Konfirmasi / Setujui Opname oleh SPV Gudang dan sinkronkan stok fisik.
     */
    public function konfirmasiSPV(Request $request, $id_opname)
    {
        $opname = StockOpname::findOrFail($id_opname);

        $opname->update([
            'status_konfirmasi' => 'dikonfirmasi_spv',
        ]);

        // Sinkronkan stok fisik gudang
        Gudang::where('kode_gudang', $opname->kode_gudang)
            ->update(['stok_tersedia' => $opname->stok_fisik, 'diperbarui_pada' => now()]);

        return redirect()->route('operasional.gudang.opname')
            ->with('sukses', "Stock Opname {$opname->nomor_opname} berhasil dikonfirmasi! Stok fisik gudang telah disinkronkan.");
    }

    /**
     * Hapus data Stock Opname dari database.
     */
    public function hapus($id_opname)
    {
        $opname = StockOpname::findOrFail($id_opname);
        $nomorOpname = $opname->nomor_opname;

        $opname->delete();

        return redirect()->route('operasional.gudang.opname')
            ->with('sukses', "Data Stock Opname {$nomorOpname} berhasil dihapus!");
    }

    /**
     * Generator kode otomatis untuk Nomor Opname.
     */
    public function buatNomorOpname(Request $request)
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
                $kandidat = 'OPN-' . $formatTanggal . '-' . $acak;
                $sudahAda = DB::table('opname_gudang')->where('nomor_opname', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('OPN-' . $formatTanggal . '-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Tanggal & Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarOpname = DB::table('opname_gudang')
            ->where('nomor_opname', 'like', 'OPN-%')
            ->pluck('nomor_opname');

        $nomorTerpakai = [];
        foreach ($daftarOpname as $kode) {
            if (preg_match('/OPN-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'OPN-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data opname awal tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahOpname = DB::table('opname_gudang')->count();
        if ($jumlahOpname === 0) {
            $gudangSatu = DB::table('list_gudang_so')->value('kode_gudang') ?? 'GDG-PUSAT';

            DB::table('opname_gudang')->insert([
                [
                    'id_opname' => 1,
                    'nomor_opname' => 'OPN-001',
                    'kode_gudang' => $gudangSatu,
                    'tanggal_opname' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'stok_sistem' => 35000,
                    'stok_fisik' => 35000,
                    'selisih' => 0,
                    'keterangan_selisih' => 'Perhitungan fisik cocok dan sesuai dengan catatan sistem logistik.',
                    'status_konfirmasi' => 'dikonfirmasi_spv',
                    'petugas_opname' => 'Ahmad Fauzi (SPV Gudang)',
                    'dibuat_pada' => Carbon::now()->subDays(2),
                ],
                [
                    'id_opname' => 2,
                    'nomor_opname' => 'OPN-002',
                    'kode_gudang' => $gudangSatu,
                    'tanggal_opname' => Carbon::now()->format('Y-m-d'),
                    'stok_sistem' => 12500,
                    'stok_fisik' => 12480,
                    'selisih' => -20,
                    'keterangan_selisih' => 'Terdapat selisih 20 zak semen robek/rusak saat proses bongkar muat armada.',
                    'status_konfirmasi' => 'draft',
                    'petugas_opname' => 'Bambang S (Staf Gudang)',
                    'dibuat_pada' => Carbon::now()->subHours(2),
                ],
            ]);
        }
    }
}
