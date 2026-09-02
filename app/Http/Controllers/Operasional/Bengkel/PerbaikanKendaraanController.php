<?php

namespace App\Http\Controllers\Operasional\Bengkel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\PerbaikanKendaraan;
use App\Models\Operasional\Kendaraan;
use Carbon\Carbon;

class PerbaikanKendaraanController extends Controller
{
    /**
     * Tampilkan riwayat Surat Perintah Kerja (SPK) Servis Kendaraan Armada.
     */
    public function index(Request $request)
    {
        $this->pastikanDataAwalTersedia();

        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');

        $query = PerbaikanKendaraan::with('kendaraan');

        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nomor_spk_perbaikan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_aset', 'like', "%{$kataKunci}%")
                  ->orWhere('keluhan_kerusakan', 'like', "%{$kataKunci}%")
                  ->orWhere('tindakan_perbaikan', 'like', "%{$kataKunci}%")
                  ->orWhere('bengkel_pelaksana', 'like', "%{$kataKunci}%")
                  ->orWhere('pengawas_kendaraan', 'like', "%{$kataKunci}%")
                  ->orWhereHas('kendaraan', function ($qK) use ($kataKunci) {
                      $qK->where('nama_aset', 'like', "%{$kataKunci}%")
                         ->orWhere('merek_aset', 'like', "%{$kataKunci}%");
                  });
            });
        }

        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $query->where('status_perbaikan', $statusFilter);
        }

        $daftarPerbaikan = $query->orderBy('tanggal_masuk', 'desc')->orderBy('dibuat_pada', 'desc')->get();

        // 4 Kartu KPI Ringkasan Servis
        $semuaPerbaikan = PerbaikanKendaraan::all();
        $totalSpk = $semuaPerbaikan->count();
        $dalamPengerjaan = $semuaPerbaikan->where('status_perbaikan', 'Dalam Proses')->count();
        $servisSelesai = $semuaPerbaikan->where('status_perbaikan', 'Selesai')->count();
        $totalBiayaServis = $semuaPerbaikan->sum('total_biaya');

        // Master Armada Kendaraan untuk Form Dropdown
        $daftarKendaraan = Kendaraan::orderBy('kode_aset', 'asc')->get();

        return view('operasional.bengkel.perbaikan', compact(
            'daftarPerbaikan',
            'kataKunci',
            'statusFilter',
            'totalSpk',
            'dalamPengerjaan',
            'servisSelesai',
            'totalBiayaServis',
            'daftarKendaraan'
        ));
    }

    /**
     * Simpan data SPK Servis baru.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'nomor_spk_perbaikan.required' => 'Nomor SPK perbaikan wajib diisi.',
            'nomor_spk_perbaikan.unique' => 'Nomor SPK sudah terdaftar.',
            'kode_aset.required' => 'Armada kendaraan wajib dipilih.',
            'kode_aset.exists' => 'Armada kendaraan tidak valid.',
            'tanggal_masuk.required' => 'Tanggal masuk servis wajib diisi.',
            'keluhan_kerusakan.required' => 'Keluhan / indikasi kerusakan wajib diisi.',
            'bengkel_pelaksana.required' => 'Bengkel pelaksana wajib dipilih.',
            'status_perbaikan.required' => 'Status perbaikan wajib dipilih.',
            'pengawas_kendaraan.required' => 'Nama pengawas kendaraan wajib diisi.',
        ];

        $validated = $request->validate([
            'nomor_spk_perbaikan' => 'required|string|max:50|unique:perbaikan_kendaraan,nomor_spk_perbaikan',
            'kode_aset' => 'required|string|max:30|exists:data_aset,kode_aset',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keluhan_kerusakan' => 'required|string',
            'tindakan_perbaikan' => 'nullable|string',
            'biaya_jasa' => 'nullable|numeric|min:0',
            'biaya_sparepart' => 'nullable|numeric|min:0',
            'bengkel_pelaksana' => 'required|string|max:100',
            'status_perbaikan' => 'required|in:Dalam Proses,Selesai,Menunggu Sparepart,Dibatalkan',
            'pengawas_kendaraan' => 'required|string|max:50',
        ], $pesanKustom);

        $biayaJasa = (float) ($validated['biaya_jasa'] ?? 0);
        $biayaSparepart = (float) ($validated['biaya_sparepart'] ?? 0);
        $totalBiaya = $biayaJasa + $biayaSparepart;

        $perbaikan = PerbaikanKendaraan::create([
            'nomor_spk_perbaikan' => strtoupper(trim($validated['nomor_spk_perbaikan'])),
            'kode_aset' => $validated['kode_aset'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'keluhan_kerusakan' => trim($validated['keluhan_kerusakan']),
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'] ? trim($validated['tindakan_perbaikan']) : null,
            'biaya_jasa' => $biayaJasa,
            'biaya_sparepart' => $biayaSparepart,
            'total_biaya' => $totalBiaya,
            'bengkel_pelaksana' => trim($validated['bengkel_pelaksana']),
            'status_perbaikan' => $validated['status_perbaikan'],
            'pengawas_kendaraan' => trim($validated['pengawas_kendaraan']),
        ]);

        return redirect()->route('operasional.bengkel.perbaikan')
            ->with('sukses', "SPK Perbaikan [{$perbaikan->nomor_spk_perbaikan}] untuk truk {$perbaikan->kode_aset} berhasil diterbitkan!");
    }

    /**
     * Ambil Detail SPK Perbaikan (JSON).
     */
    public function ambilDetail($id_perbaikan)
    {
        $perbaikan = PerbaikanKendaraan::with('kendaraan')->find($id_perbaikan);

        if (!$perbaikan) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data SPK perbaikan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $perbaikan
        ]);
    }

    /**
     * Perbarui data SPK perbaikan.
     */
    public function perbarui(Request $request, $id_perbaikan)
    {
        $perbaikan = PerbaikanKendaraan::findOrFail($id_perbaikan);

        $pesanKustom = [
            'kode_aset.required' => 'Armada kendaraan wajib dipilih.',
            'kode_aset.exists' => 'Armada kendaraan tidak valid.',
            'tanggal_masuk.required' => 'Tanggal masuk servis wajib diisi.',
            'keluhan_kerusakan.required' => 'Keluhan / kerusakan wajib diisi.',
            'bengkel_pelaksana.required' => 'Bengkel pelaksana wajib dipilih.',
            'status_perbaikan.required' => 'Status perbaikan wajib dipilih.',
            'pengawas_kendaraan.required' => 'Nama pengawas kendaraan wajib diisi.',
        ];

        $validated = $request->validate([
            'kode_aset' => 'required|string|max:30|exists:data_aset,kode_aset',
            'tanggal_masuk' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_masuk',
            'keluhan_kerusakan' => 'required|string',
            'tindakan_perbaikan' => 'nullable|string',
            'biaya_jasa' => 'nullable|numeric|min:0',
            'biaya_sparepart' => 'nullable|numeric|min:0',
            'bengkel_pelaksana' => 'required|string|max:100',
            'status_perbaikan' => 'required|in:Dalam Proses,Selesai,Menunggu Sparepart,Dibatalkan',
            'pengawas_kendaraan' => 'required|string|max:50',
        ], $pesanKustom);

        $biayaJasa = (float) ($validated['biaya_jasa'] ?? 0);
        $biayaSparepart = (float) ($validated['biaya_sparepart'] ?? 0);
        $totalBiaya = $biayaJasa + $biayaSparepart;

        // Jika status diubah jadi selesai dan tanggal_selesai kosong, isi hari ini
        $tanggalSelesai = $validated['tanggal_selesai'];
        if ($validated['status_perbaikan'] === 'Selesai' && empty($tanggalSelesai)) {
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $perbaikan->update([
            'kode_aset' => $validated['kode_aset'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'tanggal_selesai' => $tanggalSelesai,
            'keluhan_kerusakan' => trim($validated['keluhan_kerusakan']),
            'tindakan_perbaikan' => $validated['tindakan_perbaikan'] ? trim($validated['tindakan_perbaikan']) : null,
            'biaya_jasa' => $biayaJasa,
            'biaya_sparepart' => $biayaSparepart,
            'total_biaya' => $totalBiaya,
            'bengkel_pelaksana' => trim($validated['bengkel_pelaksana']),
            'status_perbaikan' => $validated['status_perbaikan'],
            'pengawas_kendaraan' => trim($validated['pengawas_kendaraan']),
        ]);

        return redirect()->route('operasional.bengkel.perbaikan')
            ->with('sukses', "Data SPK Perbaikan [{$perbaikan->nomor_spk_perbaikan}] berhasil diperbarui!");
    }

    /**
     * Perbarui Status Cepat SPK Perbaikan.
     */
    public function perbaruiStatus(Request $request, $id_perbaikan)
    {
        $perbaikan = PerbaikanKendaraan::findOrFail($id_perbaikan);

        $validated = $request->validate([
            'status_perbaikan' => 'required|in:Dalam Proses,Selesai,Menunggu Sparepart,Dibatalkan',
        ]);

        $updateData = ['status_perbaikan' => $validated['status_perbaikan']];
        if ($validated['status_perbaikan'] === 'Selesai' && empty($perbaikan->tanggal_selesai)) {
            $updateData['tanggal_selesai'] = Carbon::now()->format('Y-m-d');
        }

        $perbaikan->update($updateData);

        return redirect()->route('operasional.bengkel.perbaikan')
            ->with('sukses', "Status SPK [{$perbaikan->nomor_spk_perbaikan}] diubah menjadi '{$validated['status_perbaikan']}'!");
    }

    /**
     * Hapus data SPK Perbaikan.
     */
    public function hapus($id_perbaikan)
    {
        $perbaikan = PerbaikanKendaraan::findOrFail($id_perbaikan);
        $nomor = $perbaikan->nomor_spk_perbaikan;

        $perbaikan->delete();

        return redirect()->route('operasional.bengkel.perbaikan')
            ->with('sukses', "SPK Perbaikan [{$nomor}] berhasil dihapus!");
    }

    /**
     * Generator Nomor SPK Otomatis.
     */
    public function buatNomorSPK(Request $request)
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
                $kandidat = 'SPK-' . $formatTanggal . '-' . $acak;
                $sudahAda = DB::table('perbaikan_kendaraan')->where('nomor_spk_perbaikan', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('SPK-' . $formatTanggal . '-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Format Tanggal & Acak Anti-Tebak'
            ]);
        }

        // Mode GAP FILLING
        $daftarSpk = DB::table('perbaikan_kendaraan')
            ->where('nomor_spk_perbaikan', 'like', 'SPK-%')
            ->pluck('nomor_spk_perbaikan');

        $nomorTerpakai = [];
        foreach ($daftarSpk as $kode) {
            if (preg_match('/SPK-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'SPK-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }

    /**
     * Pastikan data awal perbaikan kendaraan tersedia.
     */
    private function pastikanDataAwalTersedia(): void
    {
        $jumlahPerbaikan = DB::table('perbaikan_kendaraan')->count();
        if ($jumlahPerbaikan === 0) {
            $trukSatu = DB::table('data_aset')->value('kode_aset') ?? 'AST-001';

            DB::table('perbaikan_kendaraan')->insert([
                [
                    'id_perbaikan' => 1,
                    'nomor_spk_perbaikan' => 'SPK-001',
                    'kode_aset' => $trukSatu,
                    'tanggal_masuk' => Carbon::now()->subDays(4)->format('Y-m-d'),
                    'tanggal_selesai' => Carbon::now()->subDays(2)->format('Y-m-d'),
                    'keluhan_kerusakan' => 'Ganti oli mesin diesel berkala, kuras oli gardan, dan pengecekan rem angin.',
                    'tindakan_perbaikan' => 'Penggantian 1 drum oli Meditran SX 15W-40, filter oli baru, dan penyetelan booster rem.',
                    'biaya_jasa' => 650000,
                    'biaya_sparepart' => 5345000,
                    'total_biaya' => 5995000,
                    'bengkel_pelaksana' => 'Bengkel Internal PBJ Karawang',
                    'status_perbaikan' => 'Selesai',
                    'pengawas_kendaraan' => 'Bambang Supriyanto (Pengawas Kendaraan)',
                    'dibuat_pada' => Carbon::now()->subDays(4),
                    'diperbarui_pada' => Carbon::now()->subDays(2),
                ],
                [
                    'id_perbaikan' => 2,
                    'nomor_spk_perbaikan' => 'SPK-002',
                    'kode_aset' => $trukSatu,
                    'tanggal_masuk' => Carbon::now()->subDays(1)->format('Y-m-d'),
                    'tanggal_selesai' => null,
                    'keluhan_kerusakan' => 'Ban belakang kiri robek terkena pecahan batu tajam di area proyek Cikarang.',
                    'tindakan_perbaikan' => 'Penggantian 2 unit ban luar Gajah Tunggal 10.00R20 dan balancing velg.',
                    'biaya_jasa' => 350000,
                    'biaya_sparepart' => 6900000,
                    'total_biaya' => 7250000,
                    'bengkel_pelaksana' => 'Bengkel Rekanan Resmi Hino Cikarang',
                    'status_perbaikan' => 'Dalam Proses',
                    'pengawas_kendaraan' => 'Bambang Supriyanto (Pengawas Kendaraan)',
                    'dibuat_pada' => Carbon::now()->subDays(1),
                    'diperbarui_pada' => Carbon::now()->subHours(3),
                ],
            ]);
        }
    }
}
