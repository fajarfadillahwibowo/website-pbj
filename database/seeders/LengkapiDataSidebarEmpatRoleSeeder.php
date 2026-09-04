<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LengkapiDataSidebarEmpatRoleSeeder extends Seeder
{
    /**
     * Seeder untuk memastikan setiap modul CRUD sidebar 4 role memiliki minimal 3 data lengkap.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $hariIni = $now->format('Y-m-d');
        $kemarin = $now->copy()->subDays(1)->format('Y-m-d');
        $duaHariLalu = $now->copy()->subDays(2)->format('Y-m-d');

        // ---------------------------------------------------------------------
        // 1. DATA GUDANG & STOK (list_gudang_so) -> Role: SPV Gudang
        // ---------------------------------------------------------------------
        $kodeBarangSemen = DB::table('data_semen')->value('kode_barang') ?? 'SMN-PCC50';

        $gudangData = [
            [
                'kode_gudang' => 'GDG-PUSAT',
                'nama_gudang' => 'Gudang Utama Distribusi Cikarang',
                'jenis_gudang' => 'Utama',
                'kode_barang' => $kodeBarangSemen,
                'plant' => 'Plant Cikarang Sentral',
                'harga_barang' => 62000,
                'stok_tersedia' => 5000,
                'distrik' => 'Bekasi',
                'sub_distrik' => 'Cikarang Selatan',
            ],
            [
                'kode_gudang' => 'GDG-CIKARANG',
                'nama_gudang' => 'Gudang Buffer Kawasan Industri Cikarang',
                'jenis_gudang' => 'Buffer',
                'kode_barang' => $kodeBarangSemen,
                'plant' => 'Plant Jababeka Blok B',
                'harga_barang' => 62000,
                'stok_tersedia' => 2500,
                'distrik' => 'Bekasi',
                'sub_distrik' => 'Cikarang Utara',
            ],
            [
                'kode_gudang' => 'GDG-CIBITUNG',
                'nama_gudang' => 'Gudang Transit Logistik Cibitung',
                'jenis_gudang' => 'Transit',
                'kode_barang' => $kodeBarangSemen,
                'plant' => 'Plant MM2100 Cibitung',
                'harga_barang' => 62500,
                'stok_tersedia' => 1800,
                'distrik' => 'Bekasi',
                'sub_distrik' => 'Cibitung Barat',
            ],
            [
                'kode_gudang' => 'GDG-KARAWANG',
                'nama_gudang' => 'Gudang Hub Distribusi Karawang',
                'jenis_gudang' => 'Distribusi',
                'kode_barang' => $kodeBarangSemen,
                'plant' => 'Plant KIIC Karawang',
                'harga_barang' => 63000,
                'stok_tersedia' => 3200,
                'distrik' => 'Karawang',
                'sub_distrik' => 'Telukjambe Timur',
            ],
        ];

        foreach ($gudangData as $g) {
            DB::table('list_gudang_so')->updateOrInsert(
                ['kode_gudang' => $g['kode_gudang']],
                array_merge($g, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 2. STOK OPNAME GUDANG (opname_gudang) -> Role: SPV Gudang & SPV Ops
        // ---------------------------------------------------------------------
        $opnameData = [
            [
                'nomor_opname' => 'OPN-202609-001',
                'kode_gudang' => 'GDG-PUSAT',
                'tanggal_opname' => $duaHariLalu,
                'stok_sistem' => 5000,
                'stok_fisik' => 5000,
                'selisih' => 0,
                'keterangan_selisih' => 'Hasil stock opname fisik cocok 100% dengan sistem.',
                'status_konfirmasi' => 'dikonfirmasi_spv',
                'petugas_opname' => 'Rian Permana (SPV Gudang)',
            ],
            [
                'nomor_opname' => 'OPN-202609-002',
                'kode_gudang' => 'GDG-CIKARANG',
                'tanggal_opname' => $kemarin,
                'stok_sistem' => 2500,
                'stok_fisik' => 2485,
                'selisih' => -15,
                'keterangan_selisih' => 'Selisih 15 zak akibat kemasan rusak handling muat, telah dibuatkan berita acara.',
                'status_konfirmasi' => 'dikonfirmasi_spv',
                'petugas_opname' => 'Hendra Gunawan',
            ],
            [
                'nomor_opname' => 'OPN-202609-003',
                'kode_gudang' => 'GDG-CIBITUNG',
                'tanggal_opname' => $hariIni,
                'stok_sistem' => 1800,
                'stok_fisik' => 1800,
                'selisih' => 0,
                'keterangan_selisih' => 'Perhitungan fisik berkala mingguan.',
                'status_konfirmasi' => 'draft',
                'petugas_opname' => 'Budi Santoso',
            ],
        ];

        foreach ($opnameData as $opn) {
            DB::table('opname_gudang')->updateOrInsert(
                ['nomor_opname' => $opn['nomor_opname']],
                array_merge($opn, [
                    'dibuat_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 3. ONGKOS ANGKUT (data_ongkos_angkut) -> Role: SPV Operasional
        // ---------------------------------------------------------------------
        $oaData = [
            [
                'kode_oa' => 'OA-CKR-BKS',
                'nama_oa' => 'Rute Plant Cikarang - Gudang Wilayah Bekasi',
                'kode_gudang' => 'GDG-PUSAT',
                'kontrak_oa' => 'KTR-PBJ-OA-2026-01',
                'muatan_oa' => 'Semen Zak 50kg',
                'harga_oa' => 4500,
                'harga_kso' => 4200,
                'harga_kso_khusus' => 4000,
                'wilayah_oa' => 'Bekasi & Cikarang',
                'keterangan' => 'Tarif trayek distribusi armada internal & KSO.',
            ],
            [
                'kode_oa' => 'OA-CBT-JKT',
                'nama_oa' => 'Rute Gudang MM2100 Cibitung - Area Jakarta Timur',
                'kode_gudang' => 'GDG-CIBITUNG',
                'kontrak_oa' => 'KTR-PBJ-OA-2026-02',
                'muatan_oa' => 'Semen Zak 50kg',
                'harga_oa' => 5500,
                'harga_kso' => 5200,
                'harga_kso_khusus' => 5000,
                'wilayah_oa' => 'DKI Jakarta Timur',
                'keterangan' => 'Rute tol Jakarta - Cikampek pintu tol Cibitung.',
            ],
            [
                'kode_oa' => 'OA-KRW-BDG',
                'nama_oa' => 'Rute Hub KIIC Karawang - Bandung Barat / Padalarang',
                'kode_gudang' => 'GDG-KARAWANG',
                'kontrak_oa' => 'KTR-PBJ-OA-2026-03',
                'muatan_oa' => 'Semen Zak 50kg',
                'harga_oa' => 8500,
                'harga_kso' => 8000,
                'harga_kso_khusus' => 7800,
                'wilayah_oa' => 'Jawa Barat (Bandung Raya)',
                'keterangan' => 'Jalur tol Cipularang via Sadang.',
            ],
        ];

        foreach ($oaData as $oa) {
            DB::table('data_ongkos_angkut')->updateOrInsert(
                ['kode_oa' => $oa['kode_oa']],
                array_merge($oa, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 4. DATA KSO (data_kso & data_ongkos_kso) -> Role: SPV Operasional
        // ---------------------------------------------------------------------
        $ksoData = [
            [
                'kode_kso' => 'KSO-001',
                'nama_kso' => 'KSO Armada Mitra Logistik Cikarang',
                'pihak_mitra' => 'PT Mitra Logistik Cikarang',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-12-31',
                'nilai_kontrak' => 500000000,
                'status_kso' => 'Aktif',
                'keterangan' => 'Kerjasama 5 unit tronton muatan semen zak kantong.',
            ],
            [
                'kode_kso' => 'KSO-002',
                'nama_kso' => 'KSO Trans Perkasa Mandiri',
                'pihak_mitra' => 'PT Trans Perkasa Mandiri',
                'tanggal_mulai' => '2026-02-01',
                'tanggal_selesai' => '2027-01-31',
                'nilai_kontrak' => 750000000,
                'status_kso' => 'Aktif',
                'keterangan' => 'Armada trailer 30 ton trayek Karawang - Jawa Barat.',
            ],
            [
                'kode_kso' => 'KSO-003',
                'nama_kso' => 'KSO Berkah Angkutan Nusantara',
                'pihak_mitra' => 'CV Berkah Angkutan Nusantara',
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-09-30',
                'nilai_kontrak' => 450000000,
                'status_kso' => 'Aktif',
                'keterangan' => 'Kerjasama logistik Colt Diesel Double (CDD).',
            ],
        ];

        foreach ($ksoData as $kso) {
            DB::table('data_kso')->updateOrInsert(
                ['kode_kso' => $kso['kode_kso']],
                array_merge($kso, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // Sub-fitur Ongkos Angkut KSO
        $ongkosKsoData = [
            [
                'kode_oa' => 'OA-KSO-001',
                'kode_kso' => 'KSO-001',
                'nama_oa' => 'Rute KSO Cikarang - Tambun',
                'muatan' => 'Semen Zak 50kg (CDD)',
                'ongkos_angkut' => 4200,
            ],
            [
                'kode_oa' => 'OA-KSO-002',
                'kode_kso' => 'KSO-002',
                'nama_oa' => 'Rute KSO Karawang - Purwakarta',
                'muatan' => 'Semen Zak 50kg (Tronton)',
                'ongkos_angkut' => 6000,
            ],
            [
                'kode_oa' => 'OA-KSO-003',
                'kode_kso' => 'KSO-003',
                'nama_oa' => 'Rute KSO Cibitung - Bekasi Barat',
                'muatan' => 'Semen Zak 50kg (CDD)',
                'ongkos_angkut' => 4500,
            ],
        ];

        foreach ($ongkosKsoData as $okoa) {
            DB::table('ongkos_kso')->updateOrInsert(
                ['kode_oa' => $okoa['kode_oa']],
                array_merge($okoa, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 5. SALES ORDER & SURAT JALAN PENGIRIMAN -> Role: Dispatcher & SPV Ops
        // ---------------------------------------------------------------------
        $firstCust = DB::table('data_customer')->value('kode_customer') ?? 'CUST-001';

        $soTambahan = [
            [
                'id_so' => 3,
                'nomor_so' => 'SO-20260903-003',
                'tanggal_so' => $kemarin,
                'kode_customer' => $firstCust,
                'kode_gudang' => 'GDG-CIKARANG',
                'jenis_pengiriman' => 'FRC',
                'jumlah_zak' => 500,
                'qty_pengambilan' => 0,
                'harga_satuan' => 62000,
                'total_harga' => 31000000,
                'status_so' => 'dikirim',
                'dibuat_oleh' => 'Staff Sales PBJ',
            ],
            [
                'id_so' => 4,
                'nomor_so' => 'SO-20260904-004',
                'tanggal_so' => $hariIni,
                'kode_customer' => $firstCust,
                'kode_gudang' => 'GDG-CIBITUNG',
                'jenis_pengiriman' => 'FRC',
                'jumlah_zak' => 400,
                'qty_pengambilan' => 0,
                'harga_satuan' => 62500,
                'total_harga' => 25000000,
                'status_so' => 'diproses',
                'dibuat_oleh' => 'Staff Sales PBJ',
            ],
        ];

        foreach ($soTambahan as $so) {
            DB::table('pembelian_so')->updateOrInsert(
                ['id_so' => $so['id_so']],
                array_merge($so, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // Data Surat Jalan Pengiriman (pengiriman)
        $daftarDrivers = DB::table('data_karyawan')->where('kategori_karyawan', 'driver')->pluck('kode_karyawan')->toArray();
        $daftarArmadas = DB::table('data_kendaraan')->pluck('kode_kendaraan')->toArray();

        $drv1 = $daftarDrivers[0] ?? 'DRV-001';
        $drv2 = $daftarDrivers[1] ?? 'DRV-002';
        $drv3 = $daftarDrivers[2] ?? 'DRV-003';

        $knd1 = $daftarArmadas[0] ?? 'KND-001';
        $knd2 = $daftarArmadas[1] ?? 'KND-002';
        $knd3 = $daftarArmadas[2] ?? 'KND-003';

        $pengirimanData = [
            [
                'nomor_surat_jalan' => 'SJ-202609-001',
                'id_so' => 1,
                'kode_driver' => $drv1,
                'kode_kendaraan' => $knd1,
                'tanggal_kirim' => $duaHariLalu . ' 08:30:00',
                'status_pengiriman' => 'terkirim',
                'keterangan' => 'Pengiriman 500 zak semen telah diterima oleh Toko Bangunan Mitra Utama.',
            ],
            [
                'nomor_surat_jalan' => 'SJ-202609-002',
                'id_so' => 2,
                'kode_driver' => $drv2,
                'kode_kendaraan' => $knd2,
                'tanggal_kirim' => $kemarin . ' 10:15:00',
                'status_pengiriman' => 'dalam_perjalanan',
                'keterangan' => 'Muatan 300 zak sedang dalam perjalanan menuju proyek via tol Cibitung.',
            ],
            [
                'nomor_surat_jalan' => 'SJ-202609-003',
                'id_so' => 3,
                'kode_driver' => $drv3,
                'kode_kendaraan' => $knd3,
                'tanggal_kirim' => $hariIni . ' 13:00:00',
                'status_pengiriman' => 'menunggu',
                'keterangan' => 'Menunggu antrean muat di Gudang Buffer Kawasan Industri Cikarang.',
            ],
        ];

        foreach ($pengirimanData as $pj) {
            DB::table('pengiriman')->updateOrInsert(
                ['nomor_surat_jalan' => $pj['nomor_surat_jalan']],
                array_merge($pj, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 6. LIST SPAREPART (list_sparepart) -> Role: Pengawas Kendaraan
        // ---------------------------------------------------------------------
        $sparepartData = [
            [
                'kode_sparepart' => 'PRT-001',
                'nama_sparepart' => 'Filter Oli Hino Dutro 130 HD Original',
                'kategori_part' => 'Filter',
                'stok_part' => 25,
                'satuan' => 'Pcs',
                'harga_satuan' => 125000,
            ],
            [
                'kode_sparepart' => 'PRT-002',
                'nama_sparepart' => 'Kampas Rem Depan Mitsubishi Fuso Fighter',
                'kategori_part' => 'Pengereman',
                'stok_part' => 16,
                'satuan' => 'Set',
                'harga_satuan' => 450000,
            ],
            [
                'kode_sparepart' => 'PRT-003',
                'nama_sparepart' => 'Pelumas Mesin Diesel Meditran SX 15W-40 (Drum)',
                'kategori_part' => 'Pelumas',
                'stok_part' => 60,
                'satuan' => 'Liter',
                'harga_satuan' => 65000,
            ],
            [
                'kode_sparepart' => 'PRT-004',
                'nama_sparepart' => 'Ban Luar Truk Gajah Tunggal 10.00-20 Lug',
                'kategori_part' => 'Ban & Velg',
                'stok_part' => 12,
                'satuan' => 'Unit',
                'harga_satuan' => 2850000,
            ],
        ];

        foreach ($sparepartData as $sp) {
            DB::table('list_sparepart')->updateOrInsert(
                ['kode_sparepart' => $sp['kode_sparepart']],
                array_merge($sp, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 7. PEMBELIAN SPAREPART (pembelian_sparepart) -> Role: Pengawas Kendaraan
        // ---------------------------------------------------------------------
        $beliPartData = [
            [
                'nomor_faktur_beli' => 'FB-202609-001',
                'kode_sparepart' => 'PRT-001',
                'tanggal_beli' => $duaHariLalu,
                'nama_supplier' => 'Toko Sparepart Hino Sentosa Cikarang',
                'jumlah_beli' => 20,
                'harga_beli' => 125000,
                'total_bayar' => 2500000,
                'dibuat_oleh' => 'Bambang Irawan (Pengawas Kendaraan)',
            ],
            [
                'nomor_faktur_beli' => 'FB-202609-002',
                'kode_sparepart' => 'PRT-002',
                'tanggal_beli' => $kemarin,
                'nama_supplier' => 'Distributor Rem Perkasa Bekasi',
                'jumlah_beli' => 10,
                'harga_beli' => 450000,
                'total_bayar' => 4500000,
                'dibuat_oleh' => 'Bambang Irawan (Pengawas Kendaraan)',
            ],
            [
                'nomor_faktur_beli' => 'FB-202609-003',
                'kode_sparepart' => 'PRT-003',
                'tanggal_beli' => $hariIni,
                'nama_supplier' => 'Agen Resmi Pertamina Lubricants Cikarang',
                'jumlah_beli' => 40,
                'harga_beli' => 65000,
                'total_bayar' => 2600000,
                'dibuat_oleh' => 'Bambang Irawan (Pengawas Kendaraan)',
            ],
        ];

        foreach ($beliPartData as $bp) {
            DB::table('pembelian_sparepart')->updateOrInsert(
                ['nomor_faktur_beli' => $bp['nomor_faktur_beli']],
                array_merge($bp, [
                    'dibuat_pada' => $now
                ])
            );
        }

        // ---------------------------------------------------------------------
        // 8. PERBAIKAN SPK KENDARAAN (perbaikan_kendaraan) -> Role: Pengawas Kendaraan
        // ---------------------------------------------------------------------
        $spkData = [
            [
                'nomor_spk_perbaikan' => 'SPK-202609-001',
                'kode_kendaraan' => $knd1,
                'tanggal_masuk' => $duaHariLalu,
                'tanggal_selesai' => $kemarin,
                'keluhan_kerusakan' => 'Servis berkala 10.000 KM, ganti oli mesin dan filter oli.',
                'tindakan_perbaikan' => 'Penggantian oli mesin Meditran SX 15W-40 dan pasang filter oli baru.',
                'bengkel_pelaksana' => 'Bengkel Internal PT PBJ',
                'status_perbaikan' => 'Selesai',
                'pengawas_kendaraan' => 'Bambang Irawan',
                'biaya_jasa' => 200000,
                'biaya_sparepart' => 350000,
                'total_biaya' => 550000,
            ],
            [
                'nomor_spk_perbaikan' => 'SPK-202609-002',
                'kode_kendaraan' => $knd2,
                'tanggal_masuk' => $kemarin,
                'tanggal_selesai' => $hariIni,
                'keluhan_kerusakan' => 'Pengereman roda depan berbunyi mendecit dan pedal rem dalam.',
                'tindakan_perbaikan' => 'Penggantian kampas rem depan kiri-kanan dan bleeding minyak rem.',
                'bengkel_pelaksana' => 'Bengkel Rekanan Mitra Fuso Bekasi',
                'status_perbaikan' => 'Selesai',
                'pengawas_kendaraan' => 'Bambang Irawan',
                'biaya_jasa' => 300000,
                'biaya_sparepart' => 900000,
                'total_biaya' => 1200000,
            ],
            [
                'nomor_spk_perbaikan' => 'SPK-202609-003',
                'kode_kendaraan' => $knd3,
                'tanggal_masuk' => $hariIni,
                'tanggal_selesai' => null,
                'keluhan_kerusakan' => 'Suhu radiator naik saat membawa muatan tanjakan, indikasi kebocoran selang pendingin.',
                'tindakan_perbaikan' => 'Pemeriksaan radiator, penggantian klem selang air radiator dan kuras coolant.',
                'bengkel_pelaksana' => 'Bengkel Internal PT PBJ',
                'status_perbaikan' => 'Dalam Proses',
                'pengawas_kendaraan' => 'Bambang Irawan',
                'biaya_jasa' => 250000,
                'biaya_sparepart' => 150000,
                'total_biaya' => 400000,
            ],
        ];

        foreach ($spkData as $spk) {
            DB::table('perbaikan_kendaraan')->updateOrInsert(
                ['nomor_spk_perbaikan' => $spk['nomor_spk_perbaikan']],
                array_merge($spk, [
                    'dibuat_pada' => $now,
                    'diperbarui_pada' => $now
                ])
            );
        }
    }
}
