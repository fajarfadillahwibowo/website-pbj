<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        sidebarTerlipat: false,
        kunciRbac: true,
        dropdownRoleTerbuka: false,
        jabatanAktif: '{{ session('kode_jabatan', 'SUPER_ADMIN') }}' || localStorage.getItem('jabatan_aktif') || 'SUPER_ADMIN',
        
        daftarRole: [
          { kode: 'SUPER_ADMIN', no: '01', nama: 'Super Admin', deskripsi: 'Kontrol akun pengguna & matriks RBAC', warna: 'purple' },
          { kode: 'SPV_KEUANGAN', no: '02', nama: 'SPV Keuangan', deskripsi: 'Finansial, AR/AP, jurnal & neraca', warna: 'emerald' },
          { kode: 'STAFF_AR', no: '03', nama: 'Staff AR', deskripsi: 'Faktur penjualan, piutang & deposit toko', warna: 'sky' },
          { kode: 'STAFF_AP', no: '04', nama: 'Staff AP', deskripsi: 'Pengeluaran kas, pembelian SO & rilisan', warna: 'amber' },
          { kode: 'DISPATCHER', no: '05', nama: 'Dispatcher', deskripsi: 'Surat jalan distribusi & armada truk', warna: 'blue' },
          { kode: 'PENGAWAS_DRIVER', no: '06', nama: 'Pengawas Driver', deskripsi: 'Data kesiapan supir armada logistik', warna: 'indigo' },
          { kode: 'SPV_GUDANG', no: '07', nama: 'SPV Gudang', deskripsi: 'Stok gudang semen & stock opname fisik', warna: 'teal' },
          { kode: 'DIREKTUR_MANAGER', no: '08', nama: 'Direktur & Manager', deskripsi: 'Eksekutif laba rugi & laporan neraca', warna: 'rose' },
          { kode: 'SPV_OPERASIONAL', no: '09', nama: 'SPV Operasional', deskripsi: 'Ongkos angkut, armada, pengiriman & KSO', warna: 'orange' },
          { kode: 'PENGAWAS_KENDARAAN', no: '10', nama: 'Pengawas Kendaraan', deskripsi: 'SPK servis, sparepart & bengkel armada', warna: 'red' }
        ],

        get roleAktifObj() {
          return this.daftarRole.find(r => r.kode === this.jabatanAktif) || this.daftarRole[1];
        },

        get labelJabatan() {
          return this.roleAktifObj.nama;
        },

        pilihRole(kode) {
          this.jabatanAktif = kode;
          localStorage.setItem('jabatan_aktif', kode);
          this.dropdownRoleTerbuka = false;
        },

        // Matriks Hak Akses RBAC Sesuai PRD 1.1 & Diagram Alur Peran
        matriksAkses: {
          SUPER_ADMIN: ['dashboard', 'admin_akun'],
          DIREKTUR_MANAGER: ['dashboard', 'laporan_neraca', 'laporan_laba_rugi'],
          SPV_KEUANGAN: [
            'dashboard', 'master_customer', 'master_barang', 'master_wilayah', 'master_karyawan',
            'ar_faktur', 'ar_piutang', 'ar_deposit', 'ap_pembelian', 'list_so', 'ap_pengeluaran', 'ap_rilisan',
            'akun_coa', 'akun_jurnal', 'akun_aset', 'jenis_aset', 'laporan_neraca', 'laporan_laba_rugi'
          ],
          STAFF_AR: [
            'dashboard', 'master_customer', 'master_barang',
            'ar_faktur', 'ar_piutang', 'ar_deposit'
          ],
          STAFF_AP: [
            'dashboard', 'ap_pembelian', 'list_so', 'gudang_stok', 'ap_pengeluaran', 'ap_rilisan'
          ],
          SPV_OPERASIONAL: [
            'dashboard', 'kirim_ongkos', 'gudang_opname', 'armada_driver', 'armada_truk', 'jenis_aset', 'kirim_sj', 'ops_kso'
          ],
          DISPATCHER: [
            'dashboard', 'armada_truk', 'jenis_aset', 'kirim_sj', 'armada_driver'
          ],
          PENGAWAS_DRIVER: [
            'dashboard', 'armada_driver'
          ],
          SPV_GUDANG: [
            'dashboard', 'gudang_stok', 'gudang_opname'
          ],
          PENGAWAS_KENDARAAN: [
            'dashboard', 'armada_truk', 'jenis_aset', 'bengkel_perbaikan', 'bengkel_pembelian_sparepart', 'bengkel_sparepart'
          ]
        },

        bisaAkses(kodeModul) {
          if (!this.kunciRbac) return true;
          const hak = this.matriksAkses[this.jabatanAktif] || [];
          return hak.includes(kodeModul);
        },

        apakahReadOnly(kodeModul) {
          if (!this.kunciRbac) return false;
          if (this.jabatanAktif === 'SUPER_ADMIN' || this.jabatanAktif === 'SPV_KEUANGAN') return false;
          if (this.jabatanAktif === 'SPV_OPERASIONAL' && kodeModul === 'armada_driver') return true;
          if (this.jabatanAktif === 'STAFF_AR') {
            const hakTulis = ['dashboard', 'master_customer', 'master_barang', 'ar_faktur', 'ar_piutang', 'ar_deposit'];
            return !hakTulis.includes(kodeModul);
          }
          if (this.jabatanAktif === 'STAFF_AP') {
            const hakTulis = ['dashboard', 'ap_pembelian', 'list_so', 'gudang_stok', 'ap_pengeluaran', 'ap_rilisan'];
            return !hakTulis.includes(kodeModul);
          }
          return false;
        }
      }"
      x-init="
        if ({{ session('fresh_login') ? 'true' : 'false' }}) {
          localStorage.setItem('jabatan_aktif', '{{ session('kode_jabatan', 'SUPER_ADMIN') }}');
          jabatanAktif = '{{ session('kode_jabatan', 'SUPER_ADMIN') }}';
          @php session()->forget('fresh_login'); @endphp
        } else if (localStorage.getItem('jabatan_aktif')) {
          jabatanAktif = localStorage.getItem('jabatan_aktif');
        }
        $watch('jabatanAktif', v => localStorage.setItem('jabatan_aktif', v))
      "
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'Sistem Informasi Akuntansi & Distribusi Semen - PT Putra Balkom Jaya')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-pbj.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['"JetBrains Mono"', 'monospace'] },
            colors: {
              surface: { DEFAULT: '#ffffff', dark: '#14161F' },
              base: { DEFAULT: '#F4F6F9', dark: '#0C0E14' },
              border: { subtle: '#EEF0F4', dark: '#252837' },
            }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      body { font-family: 'Inter', sans-serif; }
      ::-webkit-scrollbar { width: 5px; height: 5px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
      .dark ::-webkit-scrollbar-thumb { background: #334155; }
      .dropdown-shadow {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
      }
      .dark .dropdown-shadow {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
      }
    </style>
    @stack('gaya_tambahan')
</head>
<body class="h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] text-slate-900 dark:text-slate-100 antialiased flex overflow-hidden transition-colors duration-200">

    {{-- Sidebar Navigasi Menyeluruh --}}
    @include('layouts.sidebar')

    {{-- Kontainer Konten Utama --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header Topbar Enterprise --}}
        @include('layouts.header')

        {{-- Area Konten Dinamis --}}
        <main class="flex-1 overflow-y-auto p-5 sm:p-6 bg-[#F4F6F9] dark:bg-[#0C0E14]">
            @yield('konten')
        </main>
    </div>

    @stack('skrip_tambahan')
</body>
</html>
