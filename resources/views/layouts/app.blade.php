<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        sidebarTerlipat: false,
        kunciRbac: true,
        jabatanAktif: localStorage.getItem('jabatan_aktif') || '{{ session('kode_jabatan', 'SPV_KEUANGAN') }}',
        
        get labelJabatan() {
          const peta = {
            SUPER_ADMIN: 'Super Admin',
            SPV_KEUANGAN: 'SPV Keuangan',
            STAFF_AR: 'Staff AR',
            STAFF_AP: 'Staff AP',
            DISPATCHER: 'Dispatcher',
            PENGAWAS_DRIVER: 'Pengawas Driver',
            SPV_GUDANG: 'SPV Gudang',
            DIREKTUR_MANAGER: 'Direktur & Manager',
            SPV_OPERASIONAL: 'SPV Operasional',
            PENGAWAS_KENDARAAN: 'Pengawas Kendaraan'
          };
          return peta[this.jabatanAktif] || this.jabatanAktif;
        },

        // Matriks Hak Akses RBAC Sesuai PRD 1.1 & Diagram Alur Peran
        matriksAkses: {
          SUPER_ADMIN: ['dashboard', 'admin_akun'],
          DIREKTUR_MANAGER: ['dashboard', 'laporan_neraca', 'laporan_laba_rugi'],
          SPV_KEUANGAN: [
            'dashboard', 'master_customer', 'master_barang', 'master_wilayah', 'master_karyawan',
            'ar_faktur', 'ar_piutang', 'ar_deposit', 'ap_pembelian', 'ap_pengeluaran', 'ap_rilisan',
            'akun_coa', 'akun_jurnal', 'akun_aset', 'laporan_neraca', 'laporan_laba_rugi'
          ],
          STAFF_AR: [
            'dashboard', 'master_customer', 'master_barang',
            'ar_faktur', 'ar_piutang', 'ar_deposit'
          ],
          STAFF_AP: [
            'dashboard', 'ap_pembelian', 'ap_pengeluaran', 'ap_rilisan', 'gudang_stok'
          ],
          SPV_OPERASIONAL: [
            'dashboard', 'kirim_sj', 'kirim_ongkos', 'gudang_stok', 'gudang_opname',
            'armada_truk', 'armada_driver', 'bengkel_perbaikan', 'bengkel_pembelian_sparepart', 'bengkel_sparepart'
          ],
          DISPATCHER: [
            'dashboard', 'kirim_sj', 'kirim_ongkos', 'armada_truk', 'armada_driver'
          ],
          PENGAWAS_DRIVER: [
            'dashboard', 'armada_driver'
          ],
          SPV_GUDANG: [
            'dashboard', 'gudang_stok', 'gudang_opname'
          ],
          PENGAWAS_KENDARAAN: [
            'dashboard', 'bengkel_perbaikan', 'bengkel_pembelian_sparepart', 'bengkel_sparepart'
          ]
        },

        bisaAkses(kodeModul) {
          if (!this.kunciRbac) return true;
          const hak = this.matriksAkses[this.jabatanAktif] || [];
          return hak.includes(kodeModul);
        }
      }"
      x-init="$watch('jabatanAktif', v => localStorage.setItem('jabatan_aktif', v))"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'Sistem Informasi Akuntansi & Distribusi Semen')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

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
      ::-webkit-scrollbar { width: 4px; height: 4px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
      .dark ::-webkit-scrollbar-thumb { background: #334155; }
    </style>
    @stack('gaya_tambahan')
</head>
<body class="h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] text-slate-900 dark:text-slate-100 antialiased flex overflow-hidden transition-colors duration-200">

    {{-- Sidebar Navigasi Menyeluruh --}}
    @include('layouts.sidebar')

    {{-- Kontainer Konten Utama --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header Topbar --}}
        @include('layouts.header')

        {{-- Area Konten Dinamis --}}
        <main class="flex-1 overflow-y-auto p-5 sm:p-6">
            @yield('konten')
        </main>
    </div>

    @stack('skrip_tambahan')
</body>
</html>
