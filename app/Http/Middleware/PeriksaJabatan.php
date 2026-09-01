<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PeriksaJabatan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$jabatanDiizinkan
     */
    public function handle(Request $request, Closure $next, string ...$jabatanDiizinkan): Response
    {
        // 1. Cek Sesi Super Admin
        if (session()->has('super_admin_id')) {
            return $next($request);
        }

        // 2. Cek Pengguna Login Terautentikasi
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'nama_pengguna' => 'Silakan masuk ke akun Anda terlebih dahulu.',
            ]);
        }

        $pengguna = Auth::user();
        if (!$pengguna || !$pengguna->status_aktif) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'nama_pengguna' => 'Akun Anda dinonaktifkan.',
            ]);
        }

        $kodeJabatan = session('kode_jabatan') ?? $pengguna->jabatan->kode_jabatan ?? null;

        // 3. Jika route menentukan batasan role tertentu
        if (!empty($jabatanDiizinkan)) {
            if (!in_array($kodeJabatan, $jabatanDiizinkan)) {
                abort(403, 'Akses Ditolak: Jabatan Anda (' . ($pengguna->jabatan->nama_jabatan ?? $kodeJabatan) . ') tidak memiliki izin mengakses modul ini.');
            }
        }

        return $next($request);
    }
}
