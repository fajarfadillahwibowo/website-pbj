<?php

namespace App\Http\Controllers\Operasional\Armada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Operasional\Driver;
use App\Models\Autentikasi\Jabatan;

class DriverController extends Controller
{
    /**
     * Tampilkan data khusus karyawan dengan kategori Driver Supir.
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');
        $statusFilter = $request->input('status', 'semua');

        $query = Driver::with('jabatan');

        // Filter pencarian multi-kolom
        if (!empty($kataKunci)) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('nama_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('kode_karyawan', 'like', "%{$kataKunci}%")
                  ->orWhere('no_hp', 'like', "%{$kataKunci}%")
                  ->orWhere('no_identitas', 'like', "%{$kataKunci}%")
                  ->orWhere('alamat', 'like', "%{$kataKunci}%");
            });
        }

        // Filter status karyawan
        if ($statusFilter !== 'semua' && !empty($statusFilter)) {
            $query->where('status_karyawan', $statusFilter);
        }

        $daftarDriver = $query->orderBy('dibuat_pada', 'desc')->get();

        // Statistik Ringkasan Kartu KPI
        $semuaDriver = Driver::all();
        $totalDriver = $semuaDriver->count();
        $driverAktif = $semuaDriver->whereIn('status_karyawan', ['aktif', 'tetap'])->count();
        $driverKontrak = $semuaDriver->where('status_karyawan', 'kontrak')->count();
        $driverNonaktif = $semuaDriver->whereIn('status_karyawan', ['non-aktif', 'berhenti'])->count();

        // Daftar Jabatan untuk pilihan form
        $daftarJabatan = Jabatan::orderBy('nama_jabatan', 'asc')->get();

        return view('operasional.armada.driver', compact(
            'daftarDriver',
            'kataKunci',
            'statusFilter',
            'totalDriver',
            'driverAktif',
            'driverKontrak',
            'driverNonaktif',
            'daftarJabatan'
        ));
    }

    /**
     * Simpan data driver supir baru ke database.
     */
    public function simpan(Request $request)
    {
        $pesanKustom = [
            'kode_karyawan.required' => 'Kode karyawan wajib diisi.',
            'kode_karyawan.unique' => 'Kode karyawan sudah terdaftar dalam sistem.',
            'nama_karyawan.required' => 'Nama driver wajib diisi.',
            'id_jabatan.required' => 'Jabatan karyawan wajib dipilih.',
            'id_jabatan.exists' => 'Jabatan yang dipilih tidak valid.',
            'alamat.required' => 'Alamat domisili wajib diisi.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_ktp.required' => 'Nomor KTP / identitas wajib diisi.',
            'foto_ktp.image' => 'File foto KTP harus berupa gambar (JPG, PNG, WEBP).',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 3 MB.',
            'file_kontrak.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak.max' => 'Ukuran file kontrak maksimal 5 MB.',
            'status_karyawan.required' => 'Status karyawan wajib dipilih.',
        ];

        $validated = $request->validate([
            'kode_karyawan' => 'required|string|max:30|unique:data_karyawan,kode_karyawan',
            'nama_karyawan' => 'required|string|max:100',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:25',
            'no_ktp' => 'required|string|max:30',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'file_kontrak' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:5120',
            'status_karyawan' => 'required|in:aktif,kontrak,tetap,non-aktif,berhenti',
            'tanggal_mulai_kerja' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date|after_or_equal:tanggal_mulai_kerja',
        ], $pesanKustom);

        $pathFotoKtp = null;
        if ($request->hasFile('foto_ktp')) {
            $pathFotoKtp = $request->file('foto_ktp')->store('karyawan/ktp', 'public');
        }

        $pathFileKontrak = null;
        if ($request->hasFile('file_kontrak')) {
            $pathFileKontrak = $request->file('file_kontrak')->store('karyawan/kontrak', 'public');
        }

        Driver::create([
            'kode_karyawan' => trim($validated['kode_karyawan']),
            'nama_karyawan' => trim($validated['nama_karyawan']),
            'id_jabatan' => $validated['id_jabatan'],
            'kategori_karyawan' => 'driver',
            'no_identitas' => trim($validated['no_ktp']),
            'alamat' => trim($validated['alamat']),
            'no_hp' => trim($validated['no_hp']),
            'foto_ktp' => $pathFotoKtp,
            'file_kontrak' => $pathFileKontrak,
            'status_karyawan' => $validated['status_karyawan'],
            'tanggal_mulai_kerja' => $validated['tanggal_mulai_kerja'] ?? null,
            'tanggal_berhenti' => $validated['tanggal_berhenti'] ?? null,
        ]);

        return redirect()->route('operasional.armada.driver')
            ->with('sukses', "Data driver {$validated['nama_karyawan']} ({$validated['kode_karyawan']}) berhasil ditambahkan ke database!");
    }

    /**
     * Ambil data detail driver untuk modal AJAX / Alpine.js.
     */
    public function ambilDetail($kode_karyawan)
    {
        $driver = Driver::with('jabatan')->where('kode_karyawan', $kode_karyawan)->first();

        if (!$driver) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data driver tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'status' => 'sukses',
            'data' => $driver
        ]);
    }

    /**
     * Perbarui data driver supir yang ada di database.
     */
    public function perbarui(Request $request, $kode_karyawan)
    {
        $driver = Driver::where('kode_karyawan', $kode_karyawan)->firstOrFail();

        $pesanKustom = [
            'nama_karyawan.required' => 'Nama driver wajib diisi.',
            'id_jabatan.required' => 'Jabatan karyawan wajib dipilih.',
            'id_jabatan.exists' => 'Jabatan yang dipilih tidak valid.',
            'alamat.required' => 'Alamat domisili wajib diisi.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_ktp.required' => 'Nomor KTP / identitas wajib diisi.',
            'foto_ktp.image' => 'File foto KTP harus berupa gambar (JPG, PNG, WEBP).',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 3 MB.',
            'file_kontrak.mimes' => 'File kontrak harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak.max' => 'Ukuran file kontrak maksimal 5 MB.',
            'status_karyawan.required' => 'Status karyawan wajib dipilih.',
        ];

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:100',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:25',
            'no_ktp' => 'required|string|max:30',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'file_kontrak' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:5120',
            'status_karyawan' => 'required|in:aktif,kontrak,tetap,non-aktif,berhenti',
            'tanggal_mulai_kerja' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date',
        ], $pesanKustom);

        $pathFotoKtp = $driver->foto_ktp;
        if ($request->hasFile('foto_ktp')) {
            // Hapus foto lama jika ada
            if (!empty($driver->foto_ktp) && Storage::disk('public')->exists($driver->foto_ktp)) {
                Storage::disk('public')->delete($driver->foto_ktp);
            }
            $pathFotoKtp = $request->file('foto_ktp')->store('karyawan/ktp', 'public');
        }

        $pathFileKontrak = $driver->file_kontrak;
        if ($request->hasFile('file_kontrak')) {
            // Hapus file kontrak lama jika ada
            if (!empty($driver->file_kontrak) && Storage::disk('public')->exists($driver->file_kontrak)) {
                Storage::disk('public')->delete($driver->file_kontrak);
            }
            $pathFileKontrak = $request->file('file_kontrak')->store('karyawan/kontrak', 'public');
        }

        $driver->update([
            'nama_karyawan' => trim($validated['nama_karyawan']),
            'id_jabatan' => $validated['id_jabatan'],
            'no_identitas' => trim($validated['no_ktp']),
            'alamat' => trim($validated['alamat']),
            'no_hp' => trim($validated['no_hp']),
            'foto_ktp' => $pathFotoKtp,
            'file_kontrak' => $pathFileKontrak,
            'status_karyawan' => $validated['status_karyawan'],
            'tanggal_mulai_kerja' => $validated['tanggal_mulai_kerja'] ?? $driver->tanggal_mulai_kerja,
            'tanggal_berhenti' => $validated['tanggal_berhenti'] ?? $driver->tanggal_berhenti,
            'diperbarui_pada' => now(),
        ]);

        return redirect()->route('operasional.armada.driver')
            ->with('sukses', "Data driver {$driver->nama_karyawan} ({$driver->kode_karyawan}) berhasil diperbarui!");
    }

    /**
     * Hapus data driver dari database & hapus berkas fisik dari storage.
     */
    public function hapus($kode_karyawan)
    {
        $driver = Driver::where('kode_karyawan', $kode_karyawan)->firstOrFail();
        $namaDriver = $driver->nama_karyawan;

        try {
            // Hapus file foto KTP jika ada di storage
            if (!empty($driver->foto_ktp) && Storage::disk('public')->exists($driver->foto_ktp)) {
                Storage::disk('public')->delete($driver->foto_ktp);
            }

            // Hapus file kontrak jika ada di storage
            if (!empty($driver->file_kontrak) && Storage::disk('public')->exists($driver->file_kontrak)) {
                Storage::disk('public')->delete($driver->file_kontrak);
            }

            $driver->delete();

            return redirect()->route('operasional.armada.driver')
                ->with('sukses', "Data driver {$namaDriver} ({$kode_karyawan}) berhasil dihapus dari sistem! Nomor slot kode ini sekarang siap didaur ulang.");
        } catch (\Illuminate\Database\QueryException $e) {
            // Penanganan bila supir masih terikat relasi transaksi (misal: surat_jalan)
            return redirect()->route('operasional.armada.driver')
                ->with('error', "Gagal menghapus driver {$namaDriver}! Data supir ini masih terikat dengan dokumen operasional/transaksi lain di database.");
        }
    }

    /**
     * Helper endpoint cerdas untuk membuat nomor kode karyawan driver:
     * 1. Mode 'gap' (default): Menemukan slot nomor terkecil yang kosong / terhapus (Gap Filling / Auto-Reuse)
     * 2. Mode 'acak': Menghasilkan kode alfanumerik acak anti-enumerasi (DRV-XXXX)
     */
    public function buatKodeOtomatis(Request $request)
    {
        $mode = $request->input('mode', 'gap');

        if ($mode === 'acak') {
            // Karakter aman non-ambigu (tanpa 0, O, 1, I)
            $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
            $panjang = strlen($karakter);
            $kodeUnik = null;
            $percobaan = 0;

            do {
                $acak = '';
                for ($i = 0; $i < 4; $i++) {
                    $acak .= $karakter[random_int(0, $panjang - 1)];
                }
                $kandidat = 'DRV-' . $acak;
                $sudahAda = DB::table('data_karyawan')->where('kode_karyawan', $kandidat)->exists();
                if (!$sudahAda) {
                    $kodeUnik = $kandidat;
                }
                $percobaan++;
            } while (!$kodeUnik && $percobaan < 50);

            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kodeUnik ?? ('DRV-' . strtoupper(bin2hex(random_bytes(2)))),
                'keterangan' => 'Kode Alfanumerik Acak (Anti-Tebak)'
            ]);
        }

        // Mode GAP FILLING: Cari slot nomor terkecil yang kosong / terhapus
        $daftarDriver = DB::table('data_karyawan')
            ->where('kode_karyawan', 'like', 'DRV-%')
            ->pluck('kode_karyawan');

        $nomorTerpakai = [];
        foreach ($daftarDriver as $kode) {
            if (preg_match('/DRV-(\d+)/', $kode, $cocok)) {
                $nomorTerpakai[] = (int) $cocok[1];
            }
        }

        $nomorTerpakai = array_unique($nomorTerpakai);
        sort($nomorTerpakai);

        // Cari slot nomor terkecil yang kosong mulai dari 1
        $slotTersedia = 1;
        while (in_array($slotTersedia, $nomorTerpakai)) {
            $slotTersedia++;
        }

        $kodeBaru = 'DRV-' . str_pad($slotTersedia, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kodeBaru,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }
}
