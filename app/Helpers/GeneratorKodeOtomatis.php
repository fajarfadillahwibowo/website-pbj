<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class GeneratorKodeOtomatis
{
    /**
     * Menghasilkan kode urut otomatis dengan algoritma "Gap-Filling" (mengisi celah nomor terhapus/kosong terkecil).
     *
     * Logika Kerja:
     * 1. Mengambil seluruh nilai kode dari kolom tabel yang sesuai dengan awalan (prefix).
     * 2. Mengekstrak bilangan angka integer di akhir kode ke dalam array daftar nomor terpakai.
     * 3. Melakukan perulangan sekuensial mulai dari 1 ($nomorUrut = 1, 2, 3, dst).
     * 4. Begitu ditemukan nomor urut pertama yang TIDAK ADA di array terpakai (celah kosong / gap),
     *    nomor tersebut langsung diambil sebagai kode baru.
     * 5. Nomor diformat dengan panjang digit standar (contoh: 3 digit -> '001', '002', dst).
     *
     * Contoh Kasus:
     * - Terdaftar: [CUST-001, CUST-002, CUST-003] -> Hasil kode baru: CUST-004
     * - Jika CUST-002 dihapus: [CUST-001, CUST-003, CUST-004] -> Hasil kode baru: CUST-002 (mengisi celah kosong)
     * - Jika CUST-001 dihapus: [CUST-002, CUST-003] -> Hasil kode baru: CUST-001
     *
     * @param string $namaTabel Nama tabel target di database
     * @param string $namaKolom Nama kolom primary key / kode
     * @param string $awalan Awalan prefix teks (misal: 'CUST-', 'SMN-', 'WLY-', 'KRY-', 'AST-')
     * @param int $panjangDigit Jumlah digit nol di depan (default: 3)
     * @return string
     */
    public static function buatKode(string $namaTabel, string $namaKolom, string $awalan = '', int $panjangDigit = 3): string
    {
        // 1. Ambil semua kode dari database yang berawalan prefix
        $daftarKode = DB::table($namaTabel)
            ->where($namaKolom, 'like', $awalan . '%')
            ->pluck($namaKolom)
            ->toArray();

        $nomorTerpakai = [];

        // 2. Ekstrak angka urut
        foreach ($daftarKode as $kode) {
            $bagianSetelahPrefix = substr($kode, strlen($awalan));
            if (preg_match('/^(\d+)$/', $bagianSetelahPrefix, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            } elseif (preg_match('/(\d+)$/', $kode, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            }
        }

        // 3. Algoritma Gap-Filling: Cari angka positif terkecil (mulai dari 1) yang belum terpakai
        $nomorUrut = 1;
        while (isset($nomorTerpakai[$nomorUrut])) {
            $nomorUrut++;
        }

        // 4. Gabungkan prefix dengan angka terformat padding
        return $awalan . str_pad($nomorUrut, $panjangDigit, '0', STR_PAD_LEFT);
    }
}
