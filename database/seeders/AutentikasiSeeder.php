<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AutentikasiSeeder extends Seeder
{
    /**
     * Jalankan seeder akun pengguna dan jabatan demo.
     */
    public function run(): void
    {
        $passwordHash = Hash::make('password123');

        // 1. Seed Jabatan
        $jabatanList = [
            ['id_jabatan' => 1, 'kode_jabatan' => 'SUPER_ADMIN', 'nama_jabatan' => 'Super Admin'],
            ['id_jabatan' => 2, 'kode_jabatan' => 'SPV_KEUANGAN', 'nama_jabatan' => 'SPV Keuangan'],
            ['id_jabatan' => 3, 'kode_jabatan' => 'STAFF_AR', 'nama_jabatan' => 'Staff AR'],
            ['id_jabatan' => 4, 'kode_jabatan' => 'STAFF_AP', 'nama_jabatan' => 'Staff AP'],
            ['id_jabatan' => 5, 'kode_jabatan' => 'DISPATCHER', 'nama_jabatan' => 'Dispatcher'],
            ['id_jabatan' => 6, 'kode_jabatan' => 'PENGAWAS_DRIVER', 'nama_jabatan' => 'Pengawas Driver'],
            ['id_jabatan' => 7, 'kode_jabatan' => 'SPV_GUDANG', 'nama_jabatan' => 'SPV Gudang'],
            ['id_jabatan' => 8, 'kode_jabatan' => 'DIREKTUR_MANAGER', 'nama_jabatan' => 'Direktur & Manager'],
            ['id_jabatan' => 9, 'kode_jabatan' => 'SPV_OPERASIONAL', 'nama_jabatan' => 'SPV Operasional'],
            ['id_jabatan' => 10, 'kode_jabatan' => 'PENGAWAS_KENDARAAN', 'nama_jabatan' => 'Pengawas Kendaraan'],
        ];

        foreach ($jabatanList as $j) {
            DB::table('jabatan')->updateOrInsert(
                ['id_jabatan' => $j['id_jabatan']],
                [
                    'kode_jabatan' => $j['kode_jabatan'],
                    'nama_jabatan' => $j['nama_jabatan']
                ]
            );
        }

        // 2. Seed Super Account
        DB::table('super_account')->updateOrInsert(
            ['username' => 'superadmin'],
            [
                'password' => $passwordHash,
                'nama_pemilik' => 'Administrator Kontrol Akun'
            ]
        );

        // 3. Seed Karyawan Demo
        $karyawanList = [
            ['kode' => 'KRY-001', 'nama' => 'Siti Rahmawati', 'id_jabatan' => 2, 'kategori' => 'staf', 'ktp' => '3216010000000001', 'telp' => '0812-9876-0001'],
            ['kode' => 'KRY-002', 'nama' => 'Dewi Anggraeni', 'id_jabatan' => 3, 'kategori' => 'staf', 'ktp' => '3216010000000002', 'telp' => '0812-9876-0002'],
            ['kode' => 'KRY-003', 'nama' => 'Rian Hidayat', 'id_jabatan' => 4, 'kategori' => 'staf', 'ktp' => '3216010000000003', 'telp' => '0812-9876-0003'],
            ['kode' => 'KRY-004', 'nama' => 'Bambang Wijaya', 'id_jabatan' => 5, 'kategori' => 'staf', 'ktp' => '3216010000000004', 'telp' => '0812-9876-0004'],
            ['kode' => 'KRY-005', 'nama' => 'Agus Suryanto', 'id_jabatan' => 6, 'kategori' => 'driver', 'ktp' => '3216010000000005', 'telp' => '0812-9876-0005'],
            ['kode' => 'KRY-006', 'nama' => 'Hendra Gunawan', 'id_jabatan' => 7, 'kategori' => 'gudang', 'ktp' => '3216010000000006', 'telp' => '0812-9876-0006'],
            ['kode' => 'KRY-007', 'nama' => 'Ahmad Supriyadi', 'id_jabatan' => 8, 'kategori' => 'manajemen', 'ktp' => '3216010000000007', 'telp' => '0812-9876-0007'],
            ['kode' => 'KRY-008', 'nama' => 'Wahyu Pratama', 'id_jabatan' => 9, 'kategori' => 'staf', 'ktp' => '3216010000000008', 'telp' => '0812-9876-0008'],
            ['kode' => 'KRY-009', 'nama' => 'Doni Kurniawan', 'id_jabatan' => 10, 'kategori' => 'teknisi', 'ktp' => '3216010000000009', 'telp' => '0812-9876-0009'],
        ];

        foreach ($karyawanList as $k) {
            DB::table('data_karyawan')->updateOrInsert(
                ['kode_karyawan' => $k['kode']],
                [
                    'nama_karyawan' => $k['nama'],
                    'id_jabatan' => $k['id_jabatan'],
                    'kategori_karyawan' => $k['kategori'],
                    'no_identitas' => $k['ktp'],
                    'alamat' => 'Jl. Kawasan Industri No. 1, Cikarang',
                    'no_hp' => $k['telp'],
                    'status_karyawan' => 'aktif',
                    'tanggal_mulai_kerja' => '2023-01-01'
                ]
            );
        }

        // 4. Seed Akun Staf
        $akunList = [
            ['username' => 'spv_keuangan', 'kode_karyawan' => 'KRY-001', 'id_jabatan' => 2],
            ['username' => 'staff_ar', 'kode_karyawan' => 'KRY-002', 'id_jabatan' => 3],
            ['username' => 'staff_ap', 'kode_karyawan' => 'KRY-003', 'id_jabatan' => 4],
            ['username' => 'dispatcher', 'kode_karyawan' => 'KRY-004', 'id_jabatan' => 5],
            ['username' => 'pengawas_driver', 'kode_karyawan' => 'KRY-005', 'id_jabatan' => 6],
            ['username' => 'spv_gudang', 'kode_karyawan' => 'KRY-006', 'id_jabatan' => 7],
            ['username' => 'direktur', 'kode_karyawan' => 'KRY-007', 'id_jabatan' => 8],
            ['username' => 'spv_operasional', 'kode_karyawan' => 'KRY-008', 'id_jabatan' => 9],
            ['username' => 'pengawas_kendaraan', 'kode_karyawan' => 'KRY-009', 'id_jabatan' => 10],
        ];

        foreach ($akunList as $a) {
            DB::table('account')->updateOrInsert(
                ['username' => $a['username']],
                [
                    'password' => $passwordHash,
                    'kode_karyawan' => $a['kode_karyawan'],
                    'id_jabatan' => $a['id_jabatan'],
                    'status_aktif' => 1
                ]
            );
        }
    }
}
