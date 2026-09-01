<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Autentikasi\Pengguna;
use App\Models\Autentikasi\SuperAccount;

class KelolaAkunController extends Controller
{
    /**
     * Tampilkan daftar lengkap 10 akun staf dan hak akses pengguna (Super Admin).
     */
    public function index()
    {
        $superAdminList = SuperAccount::all()->map(function ($s) {
            return (object) [
                'username'     => $s->username,
                'nama_pegawai' => $s->nama_pemilik,
                'nama_jabatan' => 'Super Admin',
                'kode_jabatan' => 'SUPER_ADMIN',
                'status_aktif' => true,
                'is_super'     => true,
            ];
        });

        $penggunaList = Pengguna::with(['jabatan', 'karyawan'])->get()->map(function ($p) {
            return (object) [
                'username'     => $p->username,
                'nama_pegawai' => $p->karyawan->nama_karyawan ?? $p->username,
                'nama_jabatan' => $p->jabatan->nama_jabatan ?? 'Staf Operasional',
                'kode_jabatan' => $p->jabatan->kode_jabatan ?? 'STAF',
                'status_aktif' => $p->status_aktif,
                'is_super'     => false,
            ];
        });

        $semuaAkun = $superAdminList->concat($penggunaList);

        return view('superadmin.kelola_akun', compact('semuaAkun'));
    }
}
