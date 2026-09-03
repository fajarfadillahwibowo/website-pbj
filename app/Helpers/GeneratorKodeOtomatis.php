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

    /**
     * Menghasilkan sekumpulan N kode urut otomatis sekaligus (batch generation)
     * dengan algoritma Gap-Filling tanpa terjadi duplikasi.
     *
     * @param string $namaTabel Nama tabel target
     * @param string $namaKolom Nama kolom primary key / kode
     * @param string $awalan Awalan prefix (misal: 'AST-', 'KND-')
     * @param int $jumlah Jumlah kode berurutan yang ingin dibuat
     * @param int $panjangDigit Digit padding nol (default 3)
     * @return array Daftar kode berurutan
     */
    public static function buatBanyakKode(string $namaTabel, string $namaKolom, string $awalan = '', int $jumlah = 1, int $panjangDigit = 3): array
    {
        $daftarKode = DB::table($namaTabel)
            ->where($namaKolom, 'like', $awalan . '%')
            ->pluck($namaKolom)
            ->toArray();

        $nomorTerpakai = [];
        foreach ($daftarKode as $kode) {
            $bagianSetelahPrefix = substr($kode, strlen($awalan));
            if (preg_match('/^(\d+)$/', $bagianSetelahPrefix, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            } elseif (preg_match('/(\d+)$/', $kode, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            }
        }

        $hasilKode = [];
        $nomorUrut = 1;

        while (count($hasilKode) < $jumlah) {
            if (!isset($nomorTerpakai[$nomorUrut])) {
                $hasilKode[] = $awalan . str_pad($nomorUrut, $panjangDigit, '0', STR_PAD_LEFT);
                $nomorTerpakai[$nomorUrut] = true;
            }
            $nomorUrut++;
        }

        return $hasilKode;
    }

    /**
     * Mengembalikan awalan (prefix) 3 huruf standar berdasarkan Jabatan / Peran spesifik.
     *
     * @param int|string $jabatan ID jabatan atau nama jabatan/kode jabatan
     * @param string|null $kategori Kategori karyawan (jika 'driver', otomatis DRV-)
     * @return string
     */
    public static function ambilPrefixJabatan($jabatan, ?string $kategori = null): string
    {
        if (strtolower((string) $kategori) === 'driver') {
            return 'DRV-';
        }

        $kunci = is_numeric($jabatan) ? (int) $jabatan : strtolower(trim((string) $jabatan));

        return match ($kunci) {
            1, 'super_admin', 'super admin', 'admin'                    => 'ADM-',
            2, 'spv_keuangan', 'spv keuangan', 'keuangan'              => 'KEU-',
            3, 'staff_ar', 'staff ar', 'staf ar', 'ar'                 => 'SAR-',
            4, 'staff_ap', 'staff ap', 'staf ap', 'ap'                 => 'SAP-',
            5, 'dispatcher'                                            => 'DSP-',
            6, 'pengawas_driver', 'pengawas driver'                    => 'PDR-',
            7, 'spv_gudang', 'spv gudang', 'gudang'                    => 'GDG-',
            8, 'direktur_manager', 'direktur & manager', 'manajemen'   => 'MGR-',
            9, 'spv_operasional', 'spv operasional', 'operasional'     => 'OPS-',
            10, 'pengawas_kendaraan', 'pengawas kendaraan', 'teknisi'  => 'PKN-',
            'driver', 'supir'                                          => 'DRV-',
            default                                                    => 'STF-',
        };
    }

    /**
     * Menghasilkan kode karyawan otomatis sesuai singkatan 3 huruf dari Jabatan spesifiknya
     * dengan penomoran urut independen per jabatan (misal: KEU-001, SAR-001, SAP-001, DSP-001, PDR-001, DRV-001).
     *
     * @param int|string $jabatan
     * @param string|null $kategori
     * @param int $panjangDigit
     * @return string
     */
    public static function buatKodeJabatan($jabatan, ?string $kategori = null, int $panjangDigit = 3): string
    {
        $awalan = self::ambilPrefixJabatan($jabatan, $kategori);
        return self::buatKode('data_karyawan', 'kode_karyawan', $awalan, $panjangDigit);
    }

    /**
     * Alias kompatibilitas untuk generator kode karyawan
     */
    public static function buatKodeKaryawan(string $kategori = 'staf', int $panjangDigit = 3): string
    {
        return self::buatKodeJabatan($kategori, $kategori, $panjangDigit);
    }

    /**
     * Menghasilkan kode transaksi keuangan sekuensial berbasis tanggal (YYYYMMDD) dan nomor urut.
     * Contoh: DEP-IN-20260903-001, INV-20260903-001, SO-PBJ-20260903-001, KAS-OUT-20260903-001, JU-20260903-001
     *
     * Logika Kerja:
     * 1. Awalan: $prefix . $tanggalFormat . '-'
     * 2. Ambil semua kode yang diawali awalan tersebut pada tabel target.
     * 3. Ekstrak nomor urut numerik di bagian akhir.
     * 4. Gunakan algoritma gap-filling sekuensial (mulai dari 1) untuk nomor berikutnya.
     * 5. Format dengan padding digit (standar 3 digit: 001, 002, dst).
     *
     * @param string $namaTabel Nama tabel target di database
     * @param string $namaKolom Nama kolom kode/nomor bukti
     * @param string $prefix Awalan tipe (misal: 'DEP-IN-', 'DEP-OUT-', 'INV-', 'SO-PBJ-', 'KAS-OUT-', 'RLS-DRV-', 'JU-')
     * @param string|null $tanggal Tanggal transaksi (default: hari ini Y-m-d)
     * @param int $panjangDigit Jumlah digit nomor urut (default: 3)
     * @return string
     */
    public static function buatKodeTransaksi(string $namaTabel, string $namaKolom, string $prefix, ?string $tanggal = null, int $panjangDigit = 3): string
    {
        $tanggalFormat = $tanggal ? date('Ymd', strtotime($tanggal)) : date('Ymd');
        $awalanPenuh = $prefix . $tanggalFormat . '-';

        $daftarKode = DB::table($namaTabel)
            ->where($namaKolom, 'like', $awalanPenuh . '%')
            ->pluck($namaKolom)
            ->toArray();

        $nomorTerpakai = [];
        foreach ($daftarKode as $kode) {
            $bagianSetelahPrefix = substr($kode, strlen($awalanPenuh));
            if (preg_match('/^(\d+)$/', $bagianSetelahPrefix, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            } elseif (preg_match('/(\d+)$/', $kode, $cocok)) {
                $nomorTerpakai[(int) $cocok[1]] = true;
            }
        }

        $nomorUrut = 1;
        while (isset($nomorTerpakai[$nomorUrut])) {
            $nomorUrut++;
        }

        return $awalanPenuh . str_pad($nomorUrut, $panjangDigit, '0', STR_PAD_LEFT);
    }
}

