<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Autentikasi\Pengguna;
use App\Models\Autentikasi\SuperAccount;
use App\Models\Autentikasi\Jabatan;
use App\Models\Master\Karyawan;

class KelolaAkunController extends Controller
{
    /**
     * Tampilkan daftar lengkap akun staf dan hak akses pengguna (Super Admin).
     */
    public function index(Request $request)
    {
        $kataKunci = $request->input('cari');

        $superAdminQuery = SuperAccount::query();
        if ($kataKunci) {
            $superAdminQuery->where(function ($q) use ($kataKunci) {
                $q->where('username', 'like', "%{$kataKunci}%")
                  ->orWhere('nama_pemilik', 'like', "%{$kataKunci}%");
            });
        }

        $superAdminList = $superAdminQuery->get()->map(function ($s) {
            return (object) [
                'username'     => $s->username,
                'nama_pegawai' => $s->nama_pemilik,
                'nama_jabatan' => 'Super Admin',
                'kode_jabatan' => 'SUPER_ADMIN',
                'status_aktif' => true,
                'is_super'     => true,
            ];
        });

        $penggunaQuery = Pengguna::with(['jabatan', 'karyawan']);
        if ($kataKunci) {
            $penggunaQuery->where(function ($q) use ($kataKunci) {
                $q->where('username', 'like', "%{$kataKunci}%")
                  ->orWhereHas('karyawan', function ($k) use ($kataKunci) {
                      $k->where('nama_karyawan', 'like', "%{$kataKunci}%");
                  });
            });
        }

        $penggunaList = $penggunaQuery->get()->map(function ($p) {
            return (object) [
                'username'     => $p->username,
                'nama_pegawai' => $p->karyawan->nama_karyawan ?? $p->username,
                'nama_jabatan' => $p->jabatan->nama_jabatan ?? 'Staf Operasional',
                'kode_jabatan' => $p->jabatan->kode_jabatan ?? 'STAF',
                'status_aktif' => (bool) $p->status_aktif,
                'is_super'     => false,
            ];
        });

        $semuaAkun = $superAdminList->concat($penggunaList);
        $daftarJabatan = Jabatan::where('kode_jabatan', '!=', 'SUPER_ADMIN')->orderBy('nama_jabatan')->get();
        $daftarKaryawan = Karyawan::orderBy('nama_karyawan')->get();

        $totalAkun = $semuaAkun->count();
        $totalAktif = $semuaAkun->where('status_aktif', true)->count();
        $totalSuper = $superAdminList->count();
        $totalStaf = $penggunaList->count();

        return view('superadmin.kelola_akun', compact(
            'semuaAkun',
            'daftarJabatan',
            'daftarKaryawan',
            'kataKunci',
            'totalAkun',
            'totalAktif',
            'totalSuper',
            'totalStaf'
        ));
    }

    /**
     * Simpan akun staf baru ke tabel account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'      => 'required|string|max:50|unique:account,username|unique:super_account,username',
            'kode_karyawan' => 'required|string|exists:data_karyawan,kode_karyawan',
            'id_jabatan'    => 'required|integer|exists:jabatan,id_jabatan',
            'password'      => 'required|string|min:6',
        ], [
            'username.unique'       => 'Username sudah digunakan di sistem.',
            'kode_karyawan.exists'  => 'Data karyawan tidak ditemukan.',
            'id_jabatan.exists'     => 'Jabatan tidak valid.',
            'password.min'          => 'Kata sandi minimal 6 karakter.',
        ]);

        Pengguna::create([
            'username'      => $request->username,
            'password'      => Hash::make($request->password),
            'kode_karyawan' => $request->kode_karyawan,
            'id_jabatan'    => $request->id_jabatan,
            'status_aktif'  => true,
        ]);

        return redirect()->route('superadmin.kelola_akun')->with('sukses', "Akun staf {$request->username} berhasil ditambahkan.");
    }

    /**
     * Reset password akun menjadi 'password123'.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:account,username',
        ]);

        $pengguna = Pengguna::where('username', $request->username)->firstOrFail();
        $pengguna->update([
            'password' => Hash::make('password123'),
        ]);

        return redirect()->route('superadmin.kelola_akun')->with('sukses', "Kata sandi akun {$request->username} berhasil direset ke 'password123'.");
    }

    /**
     * Toggle status aktif/nonaktif akun.
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:account,username',
        ]);

        $pengguna = Pengguna::where('username', $request->username)->firstOrFail();
        $statusBaru = !$pengguna->status_aktif;
        $pengguna->update(['status_aktif' => $statusBaru]);

        $label = $statusBaru ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('superadmin.kelola_akun')->with('sukses', "Status akun {$request->username} berhasil {$label}.");
    }
}
