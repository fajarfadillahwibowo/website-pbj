<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Autentikasi\Pengguna;
use App\Models\Autentikasi\SuperAccount;

class AutentikasiController extends Controller
{
    /**
     * Tampilkan halaman formulir login.
     */
    public function tampilkanFormLogin()
    {
        if (Auth::check() || session()->has('super_admin_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Proses autentikasi masuk sistem (mendukung Akun Pegawai & Super Admin).
     */
    public function prosesLogin(Request $request)
    {
        $request->validate([
            'nama_pengguna' => ['required', 'string'],
            'kata_sandi'    => ['required', 'string'],
        ], [
            'nama_pengguna.required' => 'Nama pengguna wajib diisi.',
            'kata_sandi.required'    => 'Kata sandi wajib diisi.',
        ]);

        $username = trim($request->input('nama_pengguna'));
        $password = $request->input('kata_sandi');
        $ingat    = $request->boolean('ingat_saya');

        // 1. Cek Akun Super Admin
        $super = SuperAccount::where('username', $username)->first();
        if ($super && Hash::check($password, $super->password)) {
            $request->session()->regenerate();
            session([
                'super_admin_id'  => $super->id_super_account,
                'nama_pengguna'   => $super->username,
                'nama_lengkap'    => $super->nama_pemilik,
                'kode_jabatan'    => 'SUPER_ADMIN',
                'nama_jabatan'    => 'Super Admin',
                'fresh_login'     => 1,
            ]);

            $this->catatRiwayatLogin($username, 'SUKSES', $request->ip());
            return redirect()->route('dashboard');
        }

        // 2. Cek Akun Staf / Karyawan
        $pengguna = Pengguna::with(['jabatan', 'karyawan'])
            ->where('username', $username)
            ->first();

        if ($pengguna) {
            if (!$pengguna->status_aktif) {
                $this->catatRiwayatLogin($username, 'GAGAL_NONAKTIF', $request->ip());
                return back()->withErrors([
                    'nama_pengguna' => 'Akun Anda berstatus non-aktif. Hubungi Super Admin.',
                ])->onlyInput('nama_pengguna');
            }

            if (Hash::check($password, $pengguna->password)) {
                Auth::login($pengguna, $ingat);
                $request->session()->regenerate();

                session([
                    'kode_jabatan' => $pengguna->jabatan->kode_jabatan ?? 'STAF',
                    'nama_jabatan' => $pengguna->jabatan->nama_jabatan ?? 'Staf Operasional',
                    'nama_lengkap' => $pengguna->karyawan->nama_karyawan ?? $pengguna->username,
                    'fresh_login'  => 1,
                ]);

                $this->catatRiwayatLogin($username, 'SUKSES', $request->ip());
                return redirect()->intended(route('dashboard'));
            }
        }

        // Catat kegagalan login
        $this->catatRiwayatLogin($username, 'GAGAL_SANDI_SALAH', $request->ip());

        return back()->withErrors([
            'nama_pengguna' => 'Nama pengguna atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('nama_pengguna');
    }

    /**
     * Catat log aktivitas riwayat login ke tabel database.
     */
    private function catatRiwayatLogin(string $username, string $status, ?string $ip): void
    {
        try {
            DB::table('riwayat_login')->insert([
                'username'     => $username,
                'status_login' => $status,
                'ip_address'   => $ip ?? '127.0.0.1',
                'user_agent'   => request()->userAgent() ?? 'Browser',
                'waktu_login'  => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging silent jika tabel belum siap
        }
    }

    /**
     * Proses keluar sistem (logout).
     */
    public function prosesLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
