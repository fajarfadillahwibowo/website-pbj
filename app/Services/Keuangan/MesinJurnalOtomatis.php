<?php

namespace App\Services\Keuangan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\GeneratorKodeOtomatis;
use InvalidArgumentException;
use RuntimeException;

class MesinJurnalOtomatis
{
    /**
     * Mencatat satu set transaksi jurnal umum double-entry secara otomatis dan atomik.
     *
     * @param string $nomorReferensi Nomor bukti transaksi sumber (misal: INV-xxx, SO-xxx, KAS-xxx, RLS-xxx)
     * @param string $tanggalTrans Tanggal transaksi (format: YYYY-MM-DD)
     * @param array $barisJurnal Array dari baris jurnal: [['kode_akun' => '...', 'posisi' => 'Debit'|'Kredit', 'nominal' => 1000, 'keterangan' => '...'], ...]
     * @param string $keteranganUtama Keterangan transaksi secara umum
     * @param string $pembuat Nama atau username pembuat transaksi
     * @return string Nomor jurnal yang diterbitkan (misal: JU-20260903-001)
     * @throws InvalidArgumentException Jika format salah atau debit-kredit tidak seimbang
     * @throws RuntimeException Jika transaksi database gagal
     */
    public static function catatJurnal(
        string $nomorReferensi,
        string $tanggalTrans,
        array $barisJurnal,
        string $keteranganUtama,
        string $pembuat = 'Sistem'
    ): string {
        $nomorReferensi = trim($nomorReferensi);
        $tanggalTrans = substr(trim($tanggalTrans), 0, 10);

        if (empty($barisJurnal)) {
            throw new InvalidArgumentException("Baris transaksi jurnal tidak boleh kosong.");
        }

        // 1. Mekanisme Idempotency Guard: Cegah posting ganda untuk referensi yang sama
        if (!empty($nomorReferensi)) {
            $jurnalAda = DB::table('jurnal_umum')
                ->where('referensi_transaksi', $nomorReferensi)
                ->first();

            if ($jurnalAda) {
                Log::warning("MesinJurnalOtomatis: Transaksi referensi {$nomorReferensi} sudah pernah dijurnal dengan nomor {$jurnalAda->nomor_jurnal}. Posting ganda diabaikan.");
                return $jurnalAda->nomor_jurnal;
            }
        }

        // 2. Validasi Keseimbangan Debit vs Kredit
        $totalDebit = 0.00;
        $totalKredit = 0.00;
        $daftarKodeAkun = [];

        foreach ($barisJurnal as $idx => $baris) {
            $kodeAkun = trim($baris['kode_akun'] ?? '');
            $posisi = ucfirst(strtolower(trim($baris['posisi'] ?? '')));
            $nominal = (float) ($baris['nominal'] ?? 0);

            if (empty($kodeAkun)) {
                throw new InvalidArgumentException("Baris jurnal ke-" . ($idx + 1) . " tidak memiliki kode akun.");
            }

            if (!in_array($posisi, ['Debit', 'Kredit'])) {
                throw new InvalidArgumentException("Baris jurnal ke-" . ($idx + 1) . " memiliki posisi tidak valid: '{$posisi}'. Harus 'Debit' atau 'Kredit'.");
            }

            if ($nominal <= 0) {
                throw new InvalidArgumentException("Baris jurnal ke-" . ($idx + 1) . " memiliki nominal tidak valid (Rp {$nominal}). Harus bernilai positif.");
            }

            if ($posisi === 'Debit') {
                $totalDebit += $nominal;
            } else {
                $totalKredit += $nominal;
            }

            $daftarKodeAkun[] = $kodeAkun;
        }

        // Toleransi selisih floating point 0.01
        $selisih = round(abs($totalDebit - $totalKredit), 2);
        if ($selisih > 0.01) {
            $pesanError = sprintf(
                "Jurnal transaksi '%s' TIDAK SEIMBANG! Total Debit (Rp %s) tidak sama dengan Total Kredit (Rp %s). Selisih: Rp %s.",
                $nomorReferensi,
                number_format($totalDebit, 2, ',', '.'),
                number_format($totalKredit, 2, ',', '.'),
                number_format($selisih, 2, ',', '.')
            );
            Log::error("MesinJurnalOtomatis: " . $pesanError);
            throw new InvalidArgumentException($pesanError);
        }

        // 3. Validasi Keberadaan Akun di Database
        $akunDitemukan = DB::table('data_kode_akun')
            ->whereIn('kode_akun', array_unique($daftarKodeAkun))
            ->get()
            ->keyBy('kode_akun');

        foreach ($daftarKodeAkun as $kode) {
            if (!isset($akunDitemukan[$kode])) {
                throw new InvalidArgumentException("Kode akun '{$kode}' tidak terdaftar pada Master Bagan Akun (data_kode_akun).");
            }
        }

        // 4. Eksekusi Atomik Penyimpanan Jurnal & Mutasi Saldo Berjalan
        return DB::transaction(function () use (
            $nomorReferensi,
            $tanggalTrans,
            $barisJurnal,
            $keteranganUtama,
            $pembuat,
            $akunDitemukan
        ) {
            // Generate nomor jurnal berurutan sekuensial (misal: JU-20260903-001)
            $nomorJurnal = GeneratorKodeOtomatis::buatKodeTransaksi('jurnal_umum', 'nomor_jurnal', 'JU-', $tanggalTrans);

            $entriJurnal = [];
            $waktuSekarang = now();

            foreach ($barisJurnal as $baris) {
                $kodeAkun = trim($baris['kode_akun']);
                $posisi = ucfirst(strtolower(trim($baris['posisi'])));
                $nominal = (float) $baris['nominal'];
                $ketBaris = !empty($baris['keterangan']) ? trim($baris['keterangan']) : $keteranganUtama;

                // Siapkan baris insert jurnal_umum
                $entriJurnal[] = [
                    'nomor_jurnal'        => $nomorJurnal,
                    'tanggal_transaksi'   => $tanggalTrans,
                    'kode_akun'           => $kodeAkun,
                    'posisi'              => $posisi,
                    'nominal'             => $nominal,
                    'keterangan'          => substr($ketBaris, 0, 255),
                    'referensi_transaksi' => substr($nomorReferensi, 0, 50),
                    'dibuat_oleh'         => substr($pembuat, 0, 50),
                    'dibuat_pada'         => $waktuSekarang,
                ];

                // Hitung pengaruh terhadap saldo berjalan akun COA
                $dataAkun = $akunDitemukan[$kodeAkun];
                $saldoNormal = ucfirst(strtolower($dataAkun->saldo_normal));

                // Jika saldo normal Debit: Debit menambah (+), Kredit mengurangi (-)
                // Jika saldo normal Kredit: Kredit menambah (+), Debit mengurangi (-)
                if ($saldoNormal === 'Debit') {
                    $perubahanSaldo = ($posisi === 'Debit') ? $nominal : -$nominal;
                } else {
                    $perubahanSaldo = ($posisi === 'Kredit') ? $nominal : -$nominal;
                }

                DB::table('data_kode_akun')
                    ->where('kode_akun', $kodeAkun)
                    ->increment('saldo_berjalan', $perubahanSaldo);
            }

            // Insert massal ke jurnal_umum
            DB::table('jurnal_umum')->insert($entriJurnal);

            Log::info("MesinJurnalOtomatis: Berhasil memposting jurnal {$nomorJurnal} untuk referensi {$nomorReferensi} sebanyak " . count($entriJurnal) . " baris.");

            return $nomorJurnal;
        });
    }

    /**
     * Helper: Menentukan kode akun kas/bank berdasarkan metode pembayaran atau ID rekening.
     */
    public static function dapatkanKodeAkunKasBank(?int $idRekening, string $metodePembayaran = 'Tunai'): string
    {
        if (strtolower($metodePembayaran) === 'tunai' || empty($idRekening)) {
            return '1101'; // Kas Operasional Kantor
        }

        $rekening = DB::table('data_rekening')->where('id_rekening', $idRekening)->first();
        if (!$rekening) {
            return '1101';
        }

        $namaBank = strtolower($rekening->nama_bank);
        if (str_contains($namaBank, 'bca')) {
            return '1102'; // Bank BCA
        } elseif (str_contains($namaBank, 'mandiri')) {
            return '1103'; // Bank Mandiri
        } elseif (str_contains($namaBank, 'bri')) {
            return '1104'; // Bank BRI
        }

        return '1101'; // Default Kas Operasional
    }

    /**
     * Otomasi Jurnal: Faktur Penjualan Semen (AR)
     *
     * Kasus Metode Pembayaran:
     * 1. 'Tunai': Debit Kas Operasional (1101) vs Kredit Pendapatan Semen (4101)
     * 2. 'Transfer': Debit Bank (1102/1103/1104) vs Kredit Pendapatan Semen (4101)
     * 3. 'Kredit / Piutang': Debit Piutang Dagang (1105) vs Kredit Pendapatan Semen (4101)
     * 4. 'Potong Deposit': Debit Titipan Saldo Deposit (2102) vs Kredit Pendapatan Semen (4101)
     */
    public static function jurnalFakturPenjualan(
        string $nomorFaktur,
        string $tanggal,
        string $metodePembayaran,
        float $totalNetto,
        ?int $idRekening = null,
        string $pembuat = 'Staff AR',
        string $keterangan = ''
    ): string {
        $metode = trim($metodePembayaran);
        $keteranganLengkap = $keterangan ?: "Penjualan Semen Faktur {$nomorFaktur} ({$metode})";

        // Tentukan akun debit sesuai metode pembayaran
        $kodeAkunDebit = match ($metode) {
            'Tunai'               => '1101', // Kas Operasional Kantor
            'Transfer'            => self::dapatkanKodeAkunKasBank($idRekening, 'Transfer'),
            'Kredit / Piutang'    => '1105', // Piutang Dagang Customer
            'Potong Deposit'      => '2102', // Titipan Saldo Deposit Customer
            default               => '1101',
        };

        $barisJurnal = [
            [
                'kode_akun'  => $kodeAkunDebit,
                'posisi'     => 'Debit',
                'nominal'    => $totalNetto,
                'keterangan' => "Penerimaan {$metode} - {$nomorFaktur}",
            ],
            [
                'kode_akun'  => '4101', // Pendapatan Penjualan Semen
                'posisi'     => 'Kredit',
                'nominal'    => $totalNetto,
                'keterangan' => "Pendapatan Semen - {$nomorFaktur}",
            ],
        ];

        return self::catatJurnal($nomorFaktur, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }

    /**
     * Otomasi Jurnal: Pelunasan / Cicilan Piutang Customer (AR)
     *
     * Jurnal:
     * Debit: Kas/Bank (1101 / 1102 / 1103 / 1104)
     * Kredit: Piutang Dagang Customer (1105)
     */
    public static function jurnalPelunasanPiutang(
        string $nomorFaktur,
        string $tanggal,
        float $nominalBayar,
        ?int $idRekening = null,
        string $pembuat = 'Staff AR',
        string $keterangan = ''
    ): string {
        $kodeAkunKasBank = self::dapatkanKodeAkunKasBank($idRekening, $idRekening ? 'Transfer' : 'Tunai');
        $keteranganLengkap = $keterangan ?: "Pelunasan/Cicilan Piutang Faktur {$nomorFaktur}";

        // Gunakan nomor referensi unik per transaksi pelunasan
        $referensi = "PAY-" . $nomorFaktur . "-" . date('His');

        $barisJurnal = [
            [
                'kode_akun'  => $kodeAkunKasBank,
                'posisi'     => 'Debit',
                'nominal'    => $nominalBayar,
                'keterangan' => "Kas/Bank Penerimaan Cicilan {$nomorFaktur}",
            ],
            [
                'kode_akun'  => '1105', // Piutang Dagang Customer
                'posisi'     => 'Kredit',
                'nominal'    => $nominalBayar,
                'keterangan' => "Pengurangan Piutang {$nomorFaktur}",
            ],
        ];

        return self::catatJurnal($referensi, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }

    /**
     * Otomasi Jurnal: Penebusan Pembelian Sales Order Semen ke Pabrik SIG (AP)
     *
     * Jurnal:
     * Debit: Persediaan Semen Zak & Curah (1106)
     * Kredit: Kas / Bank Sumber Pembayaran (1101 / 1102 / 1103 / 1104) atau Hutang Dagang Pabrik (2101)
     */
    public static function jurnalPembelianSO(
        string $nomorSO,
        string $tanggal,
        float $totalBiaya,
        ?int $idRekening = null,
        string $metodeBayar = 'Transfer',
        string $pembuat = 'Staff AP',
        string $keterangan = ''
    ): string {
        $keteranganLengkap = $keterangan ?: "Penebusan Pembelian Semen SO {$nomorSO}";

        // Tentukan akun kredit
        $kodeAkunKredit = match ($metodeBayar) {
            'Kredit', 'Tempo' => '2101', // Hutang Dagang Pabrik Semen
            'Tunai'           => '1101', // Kas Operasional
            default           => self::dapatkanKodeAkunKasBank($idRekening, 'Transfer'),
        };

        $barisJurnal = [
            [
                'kode_akun'  => '1106', // Persediaan Semen Zak & Curah
                'posisi'     => 'Debit',
                'nominal'    => $totalBiaya,
                'keterangan' => "Penebusan Semen Masuk SO {$nomorSO}",
            ],
            [
                'kode_akun'  => $kodeAkunKredit,
                'posisi'     => 'Kredit',
                'nominal'    => $totalBiaya,
                'keterangan' => "Pembayaran SO {$nomorSO} ({$metodeBayar})",
            ],
        ];

        return self::catatJurnal($nomorSO, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }

    /**
     * Otomasi Jurnal: Pengeluaran Kas Operasional AP (BBM, Tol, Kantor, dll.)
     *
     * Jurnal:
     * Debit: Akun Beban sesuai pilihan COA (Beban BBM 6101, Beban Servis 6102, Beban Kantor 6104, dll.)
     * Kredit: Rekening Sumber Kas/Bank (1101 / 1102 / 1103 / 1104)
     */
    public static function jurnalPengeluaranKas(
        string $nomorPengeluaran,
        string $tanggal,
        string $kodeAkunBeban,
        float $nominal,
        ?int $idRekeningSumber = null,
        string $pembuat = 'Staff AP',
        string $keterangan = ''
    ): string {
        $kodeAkunKas = self::dapatkanKodeAkunKasBank($idRekeningSumber, $idRekeningSumber ? 'Transfer' : 'Tunai');
        $keteranganLengkap = $keterangan ?: "Pengeluaran Kas Operasional {$nomorPengeluaran}";

        $barisJurnal = [
            [
                'kode_akun'  => $kodeAkunBeban,
                'posisi'     => 'Debit',
                'nominal'    => $nominal,
                'keterangan' => $keteranganLengkap,
            ],
            [
                'kode_akun'  => $kodeAkunKas,
                'posisi'     => 'Kredit',
                'nominal'    => $nominal,
                'keterangan' => "Pengeluaran Kas/Bank {$nomorPengeluaran}",
            ],
        ];

        return self::catatJurnal($nomorPengeluaran, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }

    /**
     * Otomasi Jurnal: Rilisan Uang Jalan / Kas Bon Driver (AP)
     *
     * Jurnal:
     * Debit: Uang Muka / Kas Bon Supir (1107)
     * Kredit: Rekening Sumber Kas Operasional (1101 / 1102 / 1103 / 1104)
     */
    public static function jurnalRilisanUangJalan(
        string $nomorRilisan,
        string $tanggal,
        float $nominal,
        ?int $idRekeningSumber = null,
        string $pembuat = 'Staff AP',
        string $keterangan = ''
    ): string {
        $kodeAkunKas = self::dapatkanKodeAkunKasBank($idRekeningSumber, $idRekeningSumber ? 'Transfer' : 'Tunai');
        $keteranganLengkap = $keterangan ?: "Rilisan Uang Jalan Supir {$nomorRilisan}";

        $barisJurnal = [
            [
                'kode_akun'  => '1107', // Uang Muka / Kas Bon Supir
                'posisi'     => 'Debit',
                'nominal'    => $nominal,
                'keterangan' => "Kas Bon Uang Jalan - {$nomorRilisan}",
            ],
            [
                'kode_akun'  => $kodeAkunKas,
                'posisi'     => 'Kredit',
                'nominal'    => $nominal,
                'keterangan' => "Pencairan Kas Bon - {$nomorRilisan}",
            ],
        ];

        return self::catatJurnal($nomorRilisan, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }

    /**
     * Otomasi Jurnal: Top-up Deposit Masuk Customer (AR)
     *
     * Jurnal:
     * Debit: Kas / Rekening Bank Penerima (1101 / 1102 / 1103 / 1104)
     * Kredit: Titipan Saldo Deposit Customer (2102 - Kewajiban Lancar)
     */
    public static function jurnalTopUpDeposit(
        string $nomorBuktiDeposit,
        string $tanggal,
        float $nominal,
        ?int $idRekening = null,
        string $pembuat = 'Staff AR',
        string $keterangan = ''
    ): string {
        $kodeAkunKasBank = self::dapatkanKodeAkunKasBank($idRekening, $idRekening ? 'Transfer' : 'Tunai');
        $keteranganLengkap = $keterangan ?: "Penerimaan Top-up Deposit Customer {$nomorBuktiDeposit}";

        $barisJurnal = [
            [
                'kode_akun'  => $kodeAkunKasBank,
                'posisi'     => 'Debit',
                'nominal'    => $nominal,
                'keterangan' => "Penerimaan Deposit - {$nomorBuktiDeposit}",
            ],
            [
                'kode_akun'  => '2102', // Titipan Saldo Deposit Customer
                'posisi'     => 'Kredit',
                'nominal'    => $nominal,
                'keterangan' => "Titipan Deposit Masuk - {$nomorBuktiDeposit}",
            ],
        ];

        return self::catatJurnal($nomorBuktiDeposit, $tanggal, $barisJurnal, $keteranganLengkap, $pembuat);
    }
}
