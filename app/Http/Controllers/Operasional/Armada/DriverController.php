<?php

namespace App\Http\Controllers\Operasional\Armada;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        if (session('kode_jabatan') === 'SPV_OPERASIONAL') {
            return redirect()->route('operasional.armada.driver')
                ->with('error', 'Akses Ditolak! Role SPV Operasional hanya memiliki wewenang Lihat Saja (Read-Only) pada modul Driver.');
        }

        // Bersihkan karakter spasi / pemisah pada no_ktp jika ada
        if ($request->has('no_ktp')) {
            $request->merge([
                'no_ktp' => preg_replace('/[^0-9]/', '', (string) $request->input('no_ktp'))
            ]);
        }

        $pesanKustom = [
            'kode_karyawan.required' => 'Kode karyawan wajib diisi.',
            'kode_karyawan.unique' => 'Kode karyawan sudah terdaftar dalam sistem.',
            'nama_karyawan.required' => 'Nama driver wajib diisi.',
            'id_jabatan.required' => 'Jabatan karyawan wajib dipilih.',
            'id_jabatan.exists' => 'Jabatan yang dipilih tidak valid.',
            'alamat.required' => 'Alamat domisili wajib diisi.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_ktp.required' => 'Nomor KTP / NIK wajib diisi.',
            'no_ktp.digits' => 'Nomor KTP / NIK harus terdiri dari tepat 16 digit angka khas e-KTP Indonesia.',
            'no_ktp.numeric' => 'Nomor KTP / NIK hanya boleh berisi angka.',
            'foto_ktp.image' => 'Berkas Foto KTP harus berupa gambar (JPG, PNG, WEBP).',
            'foto_ktp.max' => 'Ukuran berkas Foto KTP maksimal 2 MB.',
            'file_kontrak.mimes' => 'Berkas Surat Kontrak Kerja harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak.max' => 'Ukuran berkas Surat Kontrak Kerja maksimal 2 MB.',
            'status_karyawan.required' => 'Status karyawan wajib dipilih.',
        ];

        $validated = $request->validate([
            'kode_karyawan' => 'required|string|max:30|unique:data_karyawan,kode_karyawan',
            'nama_karyawan' => 'required|string|max:100',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:25',
            'no_ktp' => 'required|numeric|digits:16',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file_kontrak' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
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

        $tanggalMulai = !empty($validated['tanggal_mulai_kerja']) 
            ? Carbon::parse($validated['tanggal_mulai_kerja'])->format('Y-m-d') 
            : null;
        $tanggalBerhenti = !empty($validated['tanggal_berhenti']) 
            ? Carbon::parse($validated['tanggal_berhenti'])->format('Y-m-d') 
            : null;

        DB::beginTransaction();
        try {
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
                'tanggal_mulai_kerja' => $tanggalMulai,
                'tanggal_berhenti' => $tanggalBerhenti,
            ]);

            DB::commit();

            return redirect()->route('operasional.armada.driver')
                ->with('sukses', "Data driver {$validated['nama_karyawan']} ({$validated['kode_karyawan']}) berhasil ditambahkan ke database!");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($pathFotoKtp && Storage::disk('public')->exists($pathFotoKtp)) {
                Storage::disk('public')->delete($pathFotoKtp);
            }
            if ($pathFileKontrak && Storage::disk('public')->exists($pathFileKontrak)) {
                Storage::disk('public')->delete($pathFileKontrak);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan driver: ' . $e->getMessage());
        }
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
        if (session('kode_jabatan') === 'SPV_OPERASIONAL') {
            return redirect()->route('operasional.armada.driver')
                ->with('error', 'Akses Ditolak! Role SPV Operasional hanya memiliki wewenang Lihat Saja (Read-Only) pada modul Driver.');
        }

        $driver = Driver::where('kode_karyawan', $kode_karyawan)->firstOrFail();

        // Bersihkan karakter spasi / pemisah pada no_ktp jika ada
        if ($request->has('no_ktp')) {
            $request->merge([
                'no_ktp' => preg_replace('/[^0-9]/', '', (string) $request->input('no_ktp'))
            ]);
        }

        $pesanKustom = [
            'nama_karyawan.required' => 'Nama driver wajib diisi.',
            'id_jabatan.required' => 'Jabatan karyawan wajib dipilih.',
            'id_jabatan.exists' => 'Jabatan yang dipilih tidak valid.',
            'alamat.required' => 'Alamat domisili wajib diisi.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_ktp.required' => 'Nomor KTP / NIK wajib diisi.',
            'no_ktp.digits' => 'Nomor KTP / NIK harus terdiri dari tepat 16 digit angka khas e-KTP Indonesia.',
            'no_ktp.numeric' => 'Nomor KTP / NIK hanya boleh berisi angka.',
            'foto_ktp.image' => 'Berkas Foto KTP harus berupa gambar (JPG, PNG, WEBP).',
            'foto_ktp.max' => 'Ukuran berkas Foto KTP maksimal 2 MB.',
            'file_kontrak.mimes' => 'Berkas Surat Kontrak Kerja harus berformat PDF, DOC, DOCX, JPG, atau PNG.',
            'file_kontrak.max' => 'Ukuran berkas Surat Kontrak Kerja maksimal 2 MB.',
            'status_karyawan.required' => 'Status karyawan wajib dipilih.',
        ];

        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:100',
            'id_jabatan' => 'required|exists:jabatan,id_jabatan',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:25',
            'no_ktp' => 'required|numeric|digits:16',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'file_kontrak' => 'nullable|file|mimes:pdf,doc,docx,jpeg,png,jpg|max:2048',
            'status_karyawan' => 'required|in:aktif,kontrak,tetap,non-aktif,berhenti',
            'tanggal_mulai_kerja' => 'nullable|date',
            'tanggal_berhenti' => 'nullable|date|after_or_equal:tanggal_mulai_kerja',
        ], $pesanKustom);

        $fotoBaru = null;
        $pathFotoKtp = $driver->foto_ktp;
        if ($request->boolean('hapus_foto_ktp')) {
            $pathFotoKtp = null;
        } elseif ($request->hasFile('foto_ktp')) {
            $fotoBaru = $request->file('foto_ktp')->store('karyawan/ktp', 'public');
            $pathFotoKtp = $fotoBaru;
        }

        $kontrakBaru = null;
        $pathFileKontrak = $driver->file_kontrak;
        if ($request->boolean('hapus_file_kontrak')) {
            $pathFileKontrak = null;
        } elseif ($request->hasFile('file_kontrak')) {
            $kontrakBaru = $request->file('file_kontrak')->store('karyawan/kontrak', 'public');
            $pathFileKontrak = $kontrakBaru;
        }

        $tanggalMulai = !empty($validated['tanggal_mulai_kerja']) 
            ? Carbon::parse($validated['tanggal_mulai_kerja'])->format('Y-m-d') 
            : null;
        $tanggalBerhenti = !empty($validated['tanggal_berhenti']) 
            ? Carbon::parse($validated['tanggal_berhenti'])->format('Y-m-d') 
            : null;

        $fotoKtpLama = $driver->foto_ktp;
        $fileKontrakLama = $driver->file_kontrak;

        DB::beginTransaction();
        try {
            $driver->update([
                'nama_karyawan' => trim($validated['nama_karyawan']),
                'id_jabatan' => $validated['id_jabatan'],
                'no_identitas' => trim($validated['no_ktp']),
                'alamat' => trim($validated['alamat']),
                'no_hp' => trim($validated['no_hp']),
                'foto_ktp' => $pathFotoKtp,
                'file_kontrak' => $pathFileKontrak,
                'status_karyawan' => $validated['status_karyawan'],
                'tanggal_mulai_kerja' => $tanggalMulai,
                'tanggal_berhenti' => $tanggalBerhenti,
                'diperbarui_pada' => now(),
            ]);

            DB::commit();

            // Hapus file fisik lama jika diganti atau dihapus setelah commit berhasil
            if (($request->boolean('hapus_foto_ktp') || $fotoBaru) && !empty($fotoKtpLama)) {
                if (Storage::disk('public')->exists($fotoKtpLama)) {
                    Storage::disk('public')->delete($fotoKtpLama);
                }
            }
            if (($request->boolean('hapus_file_kontrak') || $kontrakBaru) && !empty($fileKontrakLama)) {
                if (Storage::disk('public')->exists($fileKontrakLama)) {
                    Storage::disk('public')->delete($fileKontrakLama);
                }
            }

            return redirect()->route('operasional.armada.driver')
                ->with('sukses', "Data driver {$driver->nama_karyawan} ({$driver->kode_karyawan}) berhasil diperbarui!");
        } catch (\Exception $e) {
            DB::rollBack();
            // Bersihkan file baru jika DB update gagal
            if ($fotoBaru && Storage::disk('public')->exists($fotoBaru)) {
                Storage::disk('public')->delete($fotoBaru);
            }
            if ($kontrakBaru && Storage::disk('public')->exists($kontrakBaru)) {
                Storage::disk('public')->delete($kontrakBaru);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui driver: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data driver dari database & hapus berkas fisik dari storage.
     */
    public function hapus($kode_karyawan)
    {
        if (session('kode_jabatan') === 'SPV_OPERASIONAL') {
            return redirect()->route('operasional.armada.driver')
                ->with('error', 'Akses Ditolak! Role SPV Operasional hanya memiliki wewenang Lihat Saja (Read-Only) pada modul Driver.');
        }

        $driver = Driver::where('kode_karyawan', $kode_karyawan)->firstOrFail();
        $namaDriver = $driver->nama_karyawan;
        $fotoKtp = $driver->foto_ktp;
        $fileKontrak = $driver->file_kontrak;

        DB::beginTransaction();
        try {
            $driver->delete();
            DB::commit();

            // Hapus berkas fisik hanya setelah record berhasil dihapus dari DB
            if (!empty($fotoKtp) && Storage::disk('public')->exists($fotoKtp)) {
                Storage::disk('public')->delete($fotoKtp);
            }
            if (!empty($fileKontrak) && Storage::disk('public')->exists($fileKontrak)) {
                Storage::disk('public')->delete($fileKontrak);
            }

            return redirect()->route('operasional.armada.driver')
                ->with('sukses', "Data driver {$namaDriver} ({$kode_karyawan}) berhasil dihapus dari sistem! Nomor slot kode ini sekarang siap didaur ulang.");
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            // Penanganan bila supir masih terikat relasi transaksi (misal: surat_jalan)
            return redirect()->route('operasional.armada.driver')
                ->with('error', "Gagal menghapus driver {$namaDriver}! Data supir ini masih terikat dengan dokumen operasional/transaksi lain di database.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('operasional.armada.driver')
                ->with('error', "Gagal menghapus driver: " . $e->getMessage());
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

    /**
     * Hapus berkas lampiran (foto_ktp atau file_kontrak) milik driver.
     */
    public function hapusBerkas($kode_karyawan, $jenis_berkas)
    {
        if (session('kode_jabatan') === 'SPV_OPERASIONAL') {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Akses Ditolak: SPV Operasional hanya memiliki hak akses Lihat Saja.'
            ], 403);
        }

        $driver = Driver::where('kode_karyawan', $kode_karyawan)->first();
        if (!$driver) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Data driver tidak ditemukan.'
            ], 404);
        }

        if ($jenis_berkas === 'foto_ktp') {
            if (!empty($driver->foto_ktp) && Storage::disk('public')->exists($driver->foto_ktp)) {
                Storage::disk('public')->delete($driver->foto_ktp);
            }
            $driver->update([
                'foto_ktp' => null,
                'diperbarui_pada' => now(),
            ]);
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Berkas Foto KTP berhasil dihapus dari sistem.'
            ]);
        } elseif ($jenis_berkas === 'file_kontrak') {
            if (!empty($driver->file_kontrak) && Storage::disk('public')->exists($driver->file_kontrak)) {
                Storage::disk('public')->delete($driver->file_kontrak);
            }
            $driver->update([
                'file_kontrak' => null,
                'diperbarui_pada' => now(),
            ]);
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Berkas Surat Kontrak Kerja berhasil dihapus dari sistem.'
            ]);
        }

        return response()->json([
            'status' => 'gagal',
            'pesan' => 'Jenis berkas tidak dikenal.'
        ], 400);
    }
}
