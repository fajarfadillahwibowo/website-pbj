<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class FilterKeuanganHelper
{
    /**
     * Opsi dropdown periode standar untuk modul keuangan.
     */
    public static function opsiPeriode(): array
    {
        return [
            ['nilai' => '', 'label' => '-- Semua Periode --'],
            ['nilai' => 'bulan_ini', 'label' => 'Bulan Ini'],
            ['nilai' => '30_hari', 'label' => '30 Hari Terakhir'],
            ['nilai' => 'hari_ini', 'label' => 'Hari Ini'],
            ['nilai' => 'kustom', 'label' => 'Rentang Kustom'],
        ];
    }

    /**
     * Terapkan filter tanggal ke query builder.
     *
     * @param Builder|\Illuminate\Database\Query\Builder $query
     * @param string $kolomTanggal
     * @param string|null $periode
     * @param string|null $tglMulai
     * @param string|null $tglSelesai
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public static function terapkanFilterTanggal($query, string $kolomTanggal, ?string $periode, ?string $tglMulai = null, ?string $tglSelesai = null)
    {
        if (empty($periode) && empty($tglMulai) && empty($tglSelesai)) {
            return $query;
        }

        switch ($periode) {
            case 'hari_ini':
                return $query->whereDate($kolomTanggal, Carbon::today());

            case 'bulan_ini':
                return $query->whereMonth($kolomTanggal, Carbon::now()->month)
                             ->whereYear($kolomTanggal, Carbon::now()->year);

            case '30_hari':
                return $query->whereBetween($kolomTanggal, [
                    Carbon::now()->subDays(30)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ]);

            case 'kustom':
            default:
                if (!empty($tglMulai) && !empty($tglSelesai)) {
                    return $query->whereDate($kolomTanggal, '>=', $tglMulai)
                                 ->whereDate($kolomTanggal, '<=', $tglSelesai);
                } elseif (!empty($tglMulai)) {
                    return $query->whereDate($kolomTanggal, '>=', $tglMulai);
                } elseif (!empty($tglSelesai)) {
                    return $query->whereDate($kolomTanggal, '<=', $tglSelesai);
                }
                return $query;
        }
    }

    /**
     * Hitung berapa parameter filter yang sedang aktif.
     */
    public static function hitungFilterAktif(array $daftarFilter): int
    {
        $jumlah = 0;
        foreach ($daftarFilter as $kunci => $nilai) {
            if ($nilai !== null && $nilai !== '') {
                $jumlah++;
            }
        }
        return $jumlah;
    }
}
