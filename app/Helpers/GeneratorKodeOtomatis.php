<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class GeneratorKodeOtomatis
{
    /**
     * Menghasilkan kode urut otomatis dengan algoritma "Gap-Filling" (mengisi celah nomor terhapus/kosong terkecil).
     *
     * Contoh:
     * - Terdaftar: [CST-001, CST-003] -> Daur ulang: CST-002
     * - Terdaftar: [CST-002, CST-003] -> Daur ulang: CST-001
     */
    public static function buatKode(string $namaTabel, string $namaKolom, string $awalan = '', int $panjangDigit = 3): string
    {
        return self::buatKodeGap($namaTabel, $namaKolom, $awalan, $panjangDigit);
    }

    /**
     * Mode 1: Daur Ulang Slot Nomor Kosong / Terkecil (Gap-Filling)
     */
    public static function buatKodeGap(string $namaTabel, string $namaKolom, string $awalan = '', int $panjangDigit = 3): string
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

        $nomorUrut = 1;
        while (isset($nomorTerpakai[$nomorUrut])) {
            $nomorUrut++;
        }

        return $awalan . str_pad($nomorUrut, $panjangDigit, '0', STR_PAD_LEFT);
    }

    /**
     * Mode 2: Format Acak Anti-Tebak / Format Tanggal & Acak
     */
    public static function buatKodeAcak(string $namaTabel, string $namaKolom, string $awalan = '', bool $pakaiTanggal = false): string
    {
        $karakter = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $panjangKarakter = strlen($karakter);
        $kodeUnik = null;
        $percobaan = 0;
        $tanggalPrefix = $pakaiTanggal ? date('Ymd') . '-' : '';

        do {
            $acak = '';
            for ($i = 0; $i < 3; $i++) {
                $acak .= $karakter[random_int(0, $panjangKarakter - 1)];
            }
            $kandidat = $awalan . $tanggalPrefix . $acak;
            $sudahAda = DB::table($namaTabel)->where($namaKolom, $kandidat)->exists();
            if (!$sudahAda) {
                $kodeUnik = $kandidat;
            }
            $percobaan++;
        } while (!$kodeUnik && $percobaan < 50);

        return $kodeUnik ?? ($awalan . $tanggalPrefix . strtoupper(bin2hex(random_bytes(2))));
    }

    /**
     * Respon JSON Standar untuk API Controller
     */
    public static function responJson(string $namaTabel, string $namaKolom, string $awalan = '', string $mode = 'gap', int $panjangDigit = 3, bool $pakaiTanggal = false): JsonResponse
    {
        if ($mode === 'acak') {
            $kode = self::buatKodeAcak($namaTabel, $namaKolom, $awalan, $pakaiTanggal);
            return response()->json([
                'status' => 'sukses',
                'mode' => 'acak',
                'kode_otomatis' => $kode,
                'keterangan' => $pakaiTanggal ? 'Format Tanggal & Acak Anti-Tebak' : 'Format Acak Anti-Tebak'
            ]);
        }

        $kode = self::buatKodeGap($namaTabel, $namaKolom, $awalan, $panjangDigit);
        return response()->json([
            'status' => 'sukses',
            'mode' => 'gap',
            'kode_otomatis' => $kode,
            'keterangan' => 'Slot Nomor Terkecil Tersedia (Daur Ulang Otomatis)'
        ]);
    }
}
