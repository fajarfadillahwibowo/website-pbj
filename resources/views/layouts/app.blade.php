<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        sidebarTerlipat: localStorage.getItem('sidebar_terlipat') === 'true',
        sidebarMobileTerbuka: false,
        kunciRbac: true,
        dropdownRoleTerbuka: false,
        jabatanAktif: (function() {
            @if(session()->has('kode_jabatan'))
                var roleDariSesi = '{{ session('kode_jabatan') }}';
                try {
                    localStorage.setItem('jabatan_aktif', roleDariSesi);
                } catch(e) {}
                return roleDariSesi;
            @else
                try {
                    var tersimpan = localStorage.getItem('jabatan_aktif');
                    if (tersimpan && tersimpan !== 'null' && tersimpan !== 'undefined') {
                        return tersimpan;
                    }
                } catch (e) {}
                return 'SPV_OPERASIONAL';
            @endif
        })(),
        
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
          this.sidebarMobileTerbuka = false;
          try {
            localStorage.setItem('jabatan_aktif', kode);
          } catch(e) {}
          this.dropdownRoleTerbuka = false;
          
          // Sinkronkan ke sesi backend secara realtime
          fetch('{{ route("api.sinkronisasi_role") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ kode_jabatan: kode })
          }).catch(err => console.error('Sinkronisasi role gagal:', err));
        },

        tutupSidebarMobile() {
          this.sidebarMobileTerbuka = false;
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
          var hak = this.matriksAkses[this.jabatanAktif] || [];
          return hak.includes(kodeModul);
        },

        apakahReadOnly(kodeModul) {
          if (!this.kunciRbac) return false;
          if (this.jabatanAktif === 'SUPER_ADMIN' || this.jabatanAktif === 'SPV_KEUANGAN') return false;
          if (this.jabatanAktif === 'DIREKTUR_MANAGER') return true;
          if (this.jabatanAktif === 'SPV_OPERASIONAL' && kodeModul === 'armada_driver') return true;
          if (this.jabatanAktif === 'STAFF_AR') {
            var hakTulis = ['dashboard', 'master_customer', 'master_barang', 'ar_faktur', 'ar_piutang', 'ar_deposit'];
            return !hakTulis.includes(kodeModul);
          }
          if (this.jabatanAktif === 'STAFF_AP') {
            var hakTulis = ['dashboard', 'ap_pembelian', 'list_so', 'gudang_stok', 'ap_pengeluaran', 'ap_rilisan'];
            return !hakTulis.includes(kodeModul);
          }
          return false;
        },

        init() {
          var self = this;
          var inisialisasiSidebar = function() {
            var lebarLayar = window.innerWidth;
            if (lebarLayar < 768) {
              self.sidebarMobileTerbuka = false;
              self.sidebarTerlipat = true;
            } else if (lebarLayar < 1024) {
              self.sidebarTerlipat = true;
            } else {
              var tersimpan = localStorage.getItem('sidebar_terlipat');
              if (tersimpan !== null) {
                self.sidebarTerlipat = tersimpan === 'true';
              } else {
                self.sidebarTerlipat = false;
              }
            }
          };
          inisialisasiSidebar();

          var tanganiResize = function() {
            if (window.innerWidth >= 768) {
              self.sidebarMobileTerbuka = false;
              document.body.style.overflow = '';
            }
            if (window.innerWidth >= 1024) {
              var tersimpan = localStorage.getItem('sidebar_terlipat');
              if (tersimpan !== null) {
                self.sidebarTerlipat = tersimpan === 'true';
              } else {
                self.sidebarTerlipat = false;
              }
            } else if (window.innerWidth >= 768) {
              self.sidebarTerlipat = true;
            }
          };
          window.addEventListener('resize', tanganiResize, { passive: true });

          this.$watch('jabatanAktif', function(v) {
            try { localStorage.setItem('jabatan_aktif', v); } catch(e) {}
            fetch('{{ route("api.sinkronisasi_role") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ kode_jabatan: v })
            }).catch(function() {});
          });

          this.$watch('sidebarTerlipat', function(v) {
            if (window.innerWidth >= 1024) {
              try { localStorage.setItem('sidebar_terlipat', v ? 'true' : 'false'); } catch(e) {}
            }
          });

          this.$watch('sidebarMobileTerbuka', function(v) {
            document.body.style.overflow = v ? 'hidden' : '';
          });

          this.$nextTick(function() {
            if (typeof pulihkanPosisiSidebar === 'function') {
              pulihkanPosisiSidebar();
            }
          });
        }
      }"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=5">
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
              border: { subtle: '#EEF0F4', dark: '#252837' },
            }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      
      /* ============================================================
         CSS RESPONSIF ENTERPRISE — LINTAS SEMUA BROWSER & DEVICE
      ============================================================ */

      /* Font & Base Reset */
      *, *::before, *::after { box-sizing: border-box; }
      body { 
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        -webkit-text-size-adjust: 100%;
        -moz-text-size-adjust: 100%;
        text-size-adjust: 100%;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

      /* iOS Rubber-band-safe scrolling */
      * {
        -webkit-overflow-scrolling: touch;
      }

      /* Scrollbar Desktop */
      ::-webkit-scrollbar { width: 5px; height: 5px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
      .dark ::-webkit-scrollbar-thumb { background: #334155; }

      /* Dropdown shadow cross-browser */
      .dropdown-shadow {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.05);
      }
      .dark .dropdown-shadow {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.08);
      }

      /* ===== SIDEBAR DRAWER RESPONSIF (MOBILE & TABLET) ===== */

      /* Overlay backdrop blur untuk mobile sidebar drawer */
      .sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 40;
        background-color: rgba(0, 0, 0, 0.5);
        -webkit-backdrop-filter: blur(4px);
        backdrop-filter: blur(4px);
        touch-action: none;
      }

      /* Sidebar: Desktop = relative, Mobile = fixed drawer dari kiri */
      aside.sidebar-panel {
        position: relative;
        height: 100%;
        z-index: 30;
        transition: width 200ms ease, transform 200ms ease;
      }

      /* Mobile: sidebar jadi drawer fixed */
      @media (max-width: 767px) {
        aside.sidebar-panel {
          position: fixed;
          top: 0;
          left: 0;
          bottom: 0;
          height: 100dvh;
          width: 280px !important;
          z-index: 45;
          transform: translateX(-100%);
          box-shadow: 4px 0 30px rgba(0,0,0,0.15);
        }
        aside.sidebar-panel.sidebar-mobile-terbuka {
          transform: translateX(0);
        }
        /* Konten utama tidak perlu margin di mobile */
        .konten-wrapper-mobile {
          margin-left: 0 !important;
        }
      }

      /* Tablet: sidebar collapsed by default, dapat diexpand */
      @media (min-width: 768px) and (max-width: 1023px) {
        aside.sidebar-panel {
          position: relative;
        }
      }

      /* Safe area untuk iPhone dengan notch */
      @supports (padding: env(safe-area-inset-left)) {
        .konten-area-main {
          padding-left: max(1.25rem, env(safe-area-inset-left));
          padding-right: max(1.25rem, env(safe-area-inset-right));
          padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }
        header {
          padding-left: max(1rem, env(safe-area-inset-left));
          padding-right: max(1rem, env(safe-area-inset-right));
        }
      }

      /* Tabel responsif di mobile (scroll horizontal) */
      .table-container-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 0.75rem;
      }

      /* Input & Button touch-friendly (min 44px untuk iOS HIG) */
      @media (max-width: 767px) {
        button, a, input, select, textarea {
          min-height: 40px;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="number"],
        input[type="search"],
        select, textarea {
          font-size: 16px !important; /* Cegah zoom otomatis iOS */
        }
      }

      /* Card grid responsif */
      .grid-responsive-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
        gap: 1rem;
      }

      /* Modal responsif di mobile */
      @media (max-width: 639px) {
        [x-ref="panelModal"],
        .modal-panel {
          width: 100% !important;
          max-width: 100% !important;
          border-radius: 1rem 1rem 0 0 !important;
          position: fixed !important;
          bottom: 0 !important;
          left: 0 !important;
          right: 0 !important;
          top: auto !important;
          max-height: 92dvh !important;
          overflow-y: auto !important;
        }
      }

      /* Focus visible untuk aksesibilitas keyboard */
      :focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 4px;
      }

        /* Print optimasi */
        @media print {
          aside.sidebar-panel,
          header,
          #indikatorLoadingHalaman,
          #wadahToastGlobal {
            display: none !important;
          }
          main {
            padding: 0 !important;
            overflow: visible !important;
          }
        }

        /* ============================================================
           TRANSISI & PENCEGAHAN GETARAN LAYOUT SHIFT (SMOOTH SPA)
        ============================================================ */

        /* Transisi halus area konten utama */
        #kontenUtama {
          transition: opacity 0.18s cubic-bezier(0.16, 1, 0.3, 1);
          will-change: opacity;
        }

        /* Animasi masuk elemen yang lembut tanpa lompatan posisi kasar */
        @keyframes fade-masuk-halus {
          from {
            opacity: 0;
            transform: translateY(3px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        .animasi-masuk {
          animation: fade-masuk-halus 0.2s cubic-bezier(0.16, 1, 0.3, 1) both !important;
        }

        /* Hilangkan delay bertingkat yang bikin baris tabel lompat-lompat / bergetar */
        .tabel-bertingkat tbody tr {
          animation: none !important;
          transform: none !important;
        }

        .wadah-bertingkat > * {
          animation: fade-masuk-halus 0.18s cubic-bezier(0.16, 1, 0.3, 1) both !important;
          animation-delay: 0ms !important;
        }
    </style>
    @stack('gaya_tambahan')
</head>
<body class="h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] text-slate-900 dark:text-slate-100 antialiased flex overflow-hidden transition-colors duration-200" style="min-height: 100dvh; min-height: 100vh;">

    {{-- Indikator Loading Bar Halus (YouTube / GitHub Enterprise Style) --}}
    <div id="indikatorLoadingHalaman" 
         class="fixed top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-500 z-50 transition-all duration-300 pointer-events-none opacity-0" 
         style="width: 0%;"></div>

    {{-- Toast Notifikasi Melayang Global (Floating Alert Toast - Tanpa Menggeser Layout) --}}
    <div id="wadahToastGlobal" 
         x-data="{
             tampil: false,
             tipe: 'sukses',
             pesan: '',
             timer: null,
             buka(pesan, tipe = 'sukses') {
                 if (!pesan) return;
                 this.pesan = pesan;
                 this.tipe = tipe;
                 this.tampil = true;
                 if (this.timer) clearTimeout(this.timer);
                 this.timer = setTimeout(() => { this.tampil = false; }, 4000);
             }
         }"
         x-init="
             @if(session('sukses'))
                 buka(@js(session('sukses')), 'sukses');
             @elseif(session('gagal'))
                 buka(@js(session('gagal')), 'gagal');
             @elseif(session('error'))
                 buka(@js(session('error')), 'gagal');
             @endif
         "
         @tampilkan-toast.window="buka($event.detail.pesan, $event.detail.tipe || 'sukses')"
         x-show="tampil"
         x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
         class="fixed top-5 right-5 sm:right-6 z-[9999] max-w-sm w-full pointer-events-auto select-none">
        <div class="p-3.5 rounded-2xl shadow-2xl border flex items-center justify-between gap-3 backdrop-blur-md transition-colors"
             :class="{
                 'bg-white/95 dark:bg-[#14161F]/95 border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300': tipe === 'sukses',
                 'bg-white/95 dark:bg-[#14161F]/95 border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300': tipe === 'gagal',
                 'bg-white/95 dark:bg-[#14161F]/95 border-blue-200 dark:border-blue-500/30 text-blue-800 dark:text-blue-300': tipe === 'info'
             }">
            <div class="flex items-center gap-2.5 min-w-0">
                <template x-if="tipe === 'sukses'">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </template>
                <template x-if="tipe === 'gagal'">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                </template>
                <div class="text-xs font-semibold leading-snug break-words" x-text="pesan"></div>
            </div>
            <button type="button" @click="tampil = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Overlay Backdrop Mobile (klik di luar sidebar untuk menutup) --}}
    <div x-show="sidebarMobileTerbuka"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarMobileTerbuka = false"
         class="sidebar-overlay lg:hidden"
         aria-hidden="true">
    </div>

    {{-- Sidebar Navigasi Menyeluruh --}}
    @include('layouts.sidebar')

    {{-- Kontainer Konten Utama --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header Topbar Enterprise --}}
        @include('layouts.header')

        {{-- Area Konten Dinamis (Hanya bagian ini yang diperbarui saat klik menu sidebar) --}}
        <main id="kontenUtama" class="konten-area-main flex-1 overflow-y-auto p-4 sm:p-5 lg:p-6 bg-[#F4F6F9] dark:bg-[#0C0E14] transition-opacity duration-150">
            @yield('konten')
        </main>
    </div>

    {{-- Skrip SPA Engine & Dynamic Content Swapping (Sidebar Tidak Kerefresh) --}}
    <script>
        let abortControllerNavigasi = null;
        let intervalIndikatorLoading = null;

        function mulaiIndikatorLoading() {
            const bar = document.getElementById('indikatorLoadingHalaman');
            if (!bar) return;
            if (intervalIndikatorLoading) clearInterval(intervalIndikatorLoading);

            bar.style.transition = 'width 0.25s ease, opacity 0.15s ease';
            bar.style.opacity = '1';
            bar.style.width = '30%';

            let progress = 30;
            intervalIndikatorLoading = setInterval(() => {
                if (progress < 85) {
                    progress += Math.floor(Math.random() * 15) + 5;
                    bar.style.width = Math.min(progress, 85) + '%';
                }
            }, 150);
        }

        function selesaikanIndikatorLoading() {
            const bar = document.getElementById('indikatorLoadingHalaman');
            if (!bar) return;
            if (intervalIndikatorLoading) {
                clearInterval(intervalIndikatorLoading);
                intervalIndikatorLoading = null;
            }

            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 250);
            }, 120);
        }

        // Eksekusi skrip dinamis di dalam konten yang dimuat via AJAX
        function eksekusiSkripKonten(kontenEl) {
            if (!kontenEl) return;
            const scripts = kontenEl.querySelectorAll('script');
            scripts.forEach(script => {
                try {
                    const s = document.createElement('script');
                    Array.from(script.attributes).forEach(attr => s.setAttribute(attr.name, attr.value));
                    if (script.src) {
                        s.src = script.src;
                        s.async = false;
                    } else if (script.textContent.trim()) {
                        s.textContent = script.textContent;
                    }
                    document.body.appendChild(s);
                    // Hapus node skrip dari body setelah eksekusi agar DOM tetap bersih
                    s.remove();
                } catch (err) {
                    console.warn('Evaluasi skrip dinamis:', err);
                }
            });
        }

        // Sinkronisasi status aktif item sidebar tanpa merender ulang kontainer sidebar
        function sinkronkanSidebarAktif(sidebarBaru) {
            const navLama = document.getElementById('navigasiSidebar');
            if (!navLama || !sidebarBaru) return;

            const mapLinkBaru = new Map();
            sidebarBaru.querySelectorAll('.link-sidebar-item').forEach(link => {
                const href = link.getAttribute('href');
                if (href) {
                    try {
                        const u = new URL(href, window.location.origin);
                        mapLinkBaru.set(u.pathname, link);
                    } catch(e) {
                        mapLinkBaru.set(href, link);
                    }
                }
            });

            navLama.querySelectorAll('.link-sidebar-item').forEach(linkLama => {
                const href = linkLama.getAttribute('href');
                if (href) {
                    let linkBaru = null;
                    try {
                        const u = new URL(href, window.location.origin);
                        linkBaru = mapLinkBaru.get(u.pathname);
                    } catch(e) {
                        linkBaru = mapLinkBaru.get(href);
                    }

                    if (linkBaru) {
                        linkLama.className = linkBaru.className;
                        const iconLama = linkLama.querySelector('div.w-7.h-7');
                        const iconBaru = linkBaru.querySelector('div.w-7.h-7');
                        if (iconLama && iconBaru) {
                            iconLama.className = iconBaru.className;
                        }
                    }
                }
            });
        }

        // Helper menutup seluruh modal Alpine secara bersih
        function tutupSemuaModal() {
            document.querySelectorAll('[x-data]').forEach(el => {
                const data = el._x_dataStack ? el._x_dataStack[0] : null;
                if (data) {
                    if (typeof data.bukaModalTambah !== 'undefined') data.bukaModalTambah = false;
                    if (typeof data.bukaModalEdit !== 'undefined') data.bukaModalEdit = false;
                    if (typeof data.bukaModalHapus !== 'undefined') data.bukaModalHapus = false;
                    if (typeof data.bukaModalDetail !== 'undefined') data.bukaModalDetail = false;
                    if (typeof data.modalBuka !== 'undefined') data.modalBuka = false;
                    if (typeof data.modalTambahBuka !== 'undefined') data.modalTambahBuka = false;
                    if (typeof data.modalEditBuka !== 'undefined') data.modalEditBuka = false;
                    if (typeof data.modalHapusBuka !== 'undefined') data.modalHapusBuka = false;
                    if (typeof data.modalDetailBuka !== 'undefined') data.modalDetailBuka = false;
                    if (typeof data.tampilModal !== 'undefined') data.tampilModal = false;
                    if (typeof data.bukaModal !== 'undefined') data.bukaModal = false;
                    if (typeof data.buka !== 'undefined' && (el.classList.contains('fixed') || el.closest('.fixed'))) data.buka = false;
                }
            });
            document.body.classList.remove('overflow-hidden');
            document.body.style.overflow = '';
        }

        // Mesin Pemutakhiran Konten Parsial (SPA Swap Tanpa Getaran)
        async function muatKontenDinamis(url, targetLink = null, pushKeHistory = true, htmlSudahAda = null) {
            const kontenUtama = document.getElementById('kontenUtama');
            if (!kontenUtama) {
                window.location.href = url;
                return;
            }

            // Batalkan request navigasi sebelumnya jika ada
            if (abortControllerNavigasi) {
                abortControllerNavigasi.abort();
            }
            abortControllerNavigasi = new AbortController();

            // Kunci tinggi kontainer agar layout tidak loncat/bergetar saat konten diganti
            const tinggiSaatIni = kontenUtama.offsetHeight;
            if (tinggiSaatIni > 100) {
                kontenUtama.style.minHeight = tinggiSaatIni + 'px';
            }

            mulaiIndikatorLoading();
            kontenUtama.style.opacity = '0.6';

            try {
                let html = htmlSudahAda;
                if (!html) {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-PBJ-SPA': 'true'
                        },
                        signal: abortControllerNavigasi.signal
                    });

                    if (!response.ok) {
                        window.location.href = url;
                        return;
                    }
                    html = await response.text();
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const kontenBaru = doc.getElementById('kontenUtama') || doc.querySelector('main');
                if (!kontenBaru) {
                    window.location.href = url;
                    return;
                }

                // 1. Bersihkan Alpine tree lama pada konten untuk menghindari duplikasi memori
                if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                    try {
                        window.Alpine.destroyTree(kontenUtama);
                    } catch (e) {}
                }

                // 2. Perbarui judul dokumen dan topbar header
                const judulBaru = doc.querySelector('title')?.innerText || document.title;
                document.title = judulBaru;

                const judulHeaderLama = document.getElementById('judulHalamanAktif');
                const judulHeaderBaru = doc.getElementById('judulHalamanAktif');
                if (judulHeaderLama && judulHeaderBaru) {
                    judulHeaderLama.innerHTML = judulHeaderBaru.innerHTML;
                }

                // 3. Perbarui URL browser tanpa me-refresh halaman
                if (pushKeHistory && url) {
                    window.history.pushState({ url: url }, judulBaru, url);
                }

                // 4. Sinkronkan status aktif sidebar secara visual (SIDEBAR TIDAK PERNAH DI-REFRESH / DI-RELOAD)
                const sidebarBaru = doc.getElementById('navigasiSidebar');
                if (sidebarBaru) {
                    sinkronkanSidebarAktif(sidebarBaru);
                }

                // 5. Ganti konten dan eksekusi skrip baru
                kontenUtama.innerHTML = kontenBaru.innerHTML;
                eksekusiSkripKonten(kontenUtama);

                // 6. Inisialisasi ulang Alpine.js hanya pada area konten baru
                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    try {
                        window.Alpine.initTree(kontenUtama);
                    } catch (e) {
                        console.warn('Gagal inisialisasi Alpine tree:', e);
                    }
                }

                // 7. Kembalikan scroll konten ke bagian paling atas
                kontenUtama.scrollTop = 0;

                // 8. Ekstrak pesan toast dari respons baru jika ada
                const toastDalamDoc = doc.getElementById('wadahToastGlobal');
                let adaToast = false;
                if (toastDalamDoc) {
                    const xInitText = toastDalamDoc.getAttribute('x-init') || '';
                    const match = xInitText.match(/buka\((['"])(.*?)\1,\s*(['"])(.*?)\3\)/);
                    if (match && match[2]) {
                        window.dispatchEvent(new CustomEvent('tampilkan-toast', {
                            detail: { pesan: match[2], tipe: match[4] || 'sukses' }
                        }));
                        adaToast = true;
                    }
                }
                if (!adaToast) {
                    const wadahError = doc.querySelector('.bg-rose-50, .alert-danger');
                    if (wadahError) {
                        const pesanError = wadahError.querySelector('li')?.innerText || wadahError.innerText.trim();
                        if (pesanError) {
                            window.dispatchEvent(new CustomEvent('tampilkan-toast', {
                                detail: { pesan: pesanError.substring(0, 120), tipe: 'gagal' }
                            }));
                        }
                    }
                }

                // 9. Beri notifikasi global bahwa halaman telah berganti
                window.dispatchEvent(new CustomEvent('konten-halaman-berubah', { detail: { url: url } }));

            } catch (error) {
                if (error.name === 'AbortError') {
                    return; // Request dibatalkan karena pengguna mengklik menu lain
                }
                console.error('Navigasi SPA gagal, beralih ke navigasi browser biasa:', error);
                window.location.href = url;
            } finally {
                kontenUtama.style.opacity = '1';
                selesaikanIndikatorLoading();
                setTimeout(() => {
                    if (kontenUtama) kontenUtama.style.minHeight = '';
                }, 250);
            }
        }

        // Handler klik item sidebar
        function tanganiKlikSidebar(event, el) {
            if (!el) return;
            const url = el.getAttribute('href');
            if (!url || url === '#' || url.startsWith('javascript:')) return;

            // Biarkan browser membuka tab baru jika tombol modifikasi ditekan
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0) {
                return;
            }

            event.preventDefault();

            // Tutup drawer mobile saat menu diklik (UX smartphone)
            if (window.innerWidth < 768) {
                const htmlEl = document.documentElement;
                const alpineData = htmlEl._x_dataStack ? htmlEl._x_dataStack[0] : null;
                if (alpineData && typeof alpineData.sidebarMobileTerbuka !== 'undefined') {
                    alpineData.sidebarMobileTerbuka = false;
                }
                document.body.style.overflow = '';
            }

            // Jika URL adalah halaman yang sama, scroll halus ke atas konten
            try {
                const urlObj = new URL(url, window.location.origin);
                if (urlObj.pathname === window.location.pathname && urlObj.search === window.location.search) {
                    const kontenUtama = document.getElementById('kontenUtama');
                    if (kontenUtama) kontenUtama.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
            } catch(e) {}

            muatKontenDinamis(url, el);
        }

        // Tangani navigasi tombol Back / Forward browser
        window.addEventListener('popstate', function(event) {
            muatKontenDinamis(window.location.href, null, false);
        });

        // Tangani form GET (Filter) & Form Mutasi (POST/PUT/DELETE) secara mulus tanpa getaran layout
        document.addEventListener('submit', function(event) {
            if (event.defaultPrevented) return;

            const form = event.target;
            if (!form || !form.closest('#kontenUtama')) return;

            // Abaikan form khusus yang harus diproses secara browser native
            const action = form.getAttribute('action') || window.location.href;
            if (form.hasAttribute('data-native') || 
                form.getAttribute('target') === '_blank' || 
                action.includes('/logout') ||
                action.includes('/export') ||
                action.includes('/cetak') ||
                action.includes('/download')) {
                return;
            }

            const method = (form.getAttribute('method') || 'GET').toUpperCase();

            // 1. Form GET (Filter & Pencarian Tabel)
            if (method === 'GET') {
                event.preventDefault();
                const url = new URL(action, window.location.origin);
                const formData = new FormData(form);
                const params = new URLSearchParams();

                for (const [key, value] of formData.entries()) {
                    if (value !== '') {
                        params.append(key, value);
                    }
                }
                url.search = params.toString();
                muatKontenDinamis(url.href);
                return;
            }

            // 2. Form Mutasi Data (POST / PUT / DELETE)
            if (method === 'POST') {
                event.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-60', 'cursor-wait');
                }

                mulaiIndikatorLoading();
                const formData = new FormData(form);

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-PBJ-SPA': 'true'
                    },
                    body: formData
                })
                .then(async response => {
                    tutupSemuaModal();
                    if (response.redirected || response.ok) {
                        const html = await response.text();
                        muatKontenDinamis(response.url || window.location.href, null, true, html);
                    } else if (response.status === 422) {
                        const contentType = response.headers.get('content-type') || '';
                        if (contentType.includes('application/json')) {
                            const data = await response.json();
                            const pesanError = Object.values(data.errors || {})[0]?.[0] || data.message || 'Validasi data gagal';
                            window.dispatchEvent(new CustomEvent('tampilkan-toast', { detail: { pesan: pesanError, tipe: 'gagal' } }));
                        } else {
                            const html = await response.text();
                            muatKontenDinamis(response.url || window.location.href, null, false, html);
                        }
                    } else {
                        // Fallback reload jika error 500
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error('Mutasi data SPA gagal, beralih ke submit native:', err);
                    form.submit();
                })
                .finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-60', 'cursor-wait');
                    }
                    selesaikanIndikatorLoading();
                });
            }
        });

        // Interseptor tautan internal di dalam konten utama (seperti pintasan dashboard / navigasi antar modul)
        document.addEventListener('click', function(event) {
            const link = event.target.closest('a');
            if (!link) return;

            // Abaikan klik kanan, middle click, atau jika tombol kombinasi ditekan
            if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;

            const href = link.getAttribute('href');
            if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.getAttribute('target') === '_blank' || link.hasAttribute('download')) return;

            // Jika tautan berada di dalam konten utama dan mengarah ke sistem internal
            const diKontenUtama = link.closest('#kontenUtama');
            if (diKontenUtama) {
                try {
                    const urlTujuan = new URL(href, window.location.origin);
                    if (urlTujuan.origin === window.location.origin && 
                        !href.includes('/export') && 
                        !href.includes('/cetak') && 
                        !href.includes('/download') && 
                        !href.includes('/logout')) {
                        event.preventDefault();
                        muatKontenDinamis(urlTujuan.href, link);
                    }
                } catch(e) {}
            }
        });

        // Pemulihan posisi scroll sidebar saat halaman pertama kali dimuat
        function pulihkanPosisiSidebar() {
            const nav = document.getElementById('navigasiSidebar');
            if (!nav) return;

            const linkAktif = nav.querySelector('a.link-sidebar-aktif');
            if (linkAktif) {
                setTimeout(() => {
                    const navRect = nav.getBoundingClientRect();
                    const linkRect = linkAktif.getBoundingClientRect();
                    if (linkRect.top < navRect.top || linkRect.bottom > navRect.bottom) {
                        linkAktif.scrollIntoView({ block: 'nearest', behavior: 'instant' });
                    }
                }, 50);
            }
        }

        // =========================================================================
        // PENGAMAN GLOBAL: INPUT HANYA ANGKA & FORMAT RUPIAH OTOMATIS
        // =========================================================================
        const DAFTAR_FIELD_ANGKA = [
            'nik', 'no_ktp', 'no_identitas', 'no_hp', 'telepon', 'no_telp', 'no_hp_toko', 
            'nomor_rekening', 'jumlah_zak', 'stok_sistem', 'stok_fisik', 'stok_tersedia', 
            'stok_part', 'jumlah_beli', 'jumlah_unit', 'tahun_pembuatan', 'umur_manfaat', 
            'odometer_km', 'periode_tahun'
        ];

        function apakahFieldHanyaAngka(target) {
            if (!target || target.tagName !== 'INPUT') return false;
            if (target.dataset.inputRupiah === 'true') return false; // Dikelola komponen x-input-rupiah
            if (target.dataset.hanyaAngka === 'true') return true;
            if (target.getAttribute('inputmode') === 'numeric') return true;
            if (target.type === 'number') return true;
            const namaField = (target.name || '').toLowerCase();
            return DAFTAR_FIELD_ANGKA.some(field => namaField.includes(field));
        }

        // 1. Blokir ketikan huruf di tingkat Keydown
        document.addEventListener('keydown', function(event) {
            const target = event.target;
            if (!apakahFieldHanyaAngka(target)) return;

            // Izinkan tombol kontrol dan navigasi
            const tombolIzin = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Home', 'End', 'Enter', 'Escape'];
            if (tombolIzin.includes(event.key)) return;

            // Izinkan shortcut keyboard (Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+Z, Meta/Cmd)
            if (event.ctrlKey || event.metaKey) return;

            // Jika bukan angka 0-9, cegah pengetikan huruf
            if (!/^[0-9]$/.test(event.key)) {
                event.preventDefault();
            }
        }, true);

        // 2. Pembersihan karakter non-angka saat paste / input
        document.addEventListener('input', function(event) {
            const target = event.target;
            if (!apakahFieldHanyaAngka(target)) return;

            if (target.type !== 'number') {
                const nilaiAwal = target.value;
                const nilaiBersih = nilaiAwal.replace(/[^0-9]/g, '');
                if (nilaiAwal !== nilaiBersih) {
                    target.value = nilaiBersih;
                    target.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }
        }, true);

        // =========================================================================
        // HELPER GLOBAL: PAGINASI TABEL INTERAKTIF REAKTIF & MULTI-SELECT (ALPINE.JS)
        // =========================================================================
        function tabelPaginasi(opsi = {}) {
            return {
                halamanSekarang: 1,
                barisPerHalaman: parseInt(opsi.defaultBaris) || 10,
                totalData: parseInt(opsi.totalData) || 0,
                daftarTerpilih: [],
                initPaginasi(total) {
                    if (typeof total !== 'undefined') {
                        this.totalData = parseInt(total) || 0;
                    }
                },
                get totalHalaman() {
                    return Math.max(1, Math.ceil(this.totalData / this.barisPerHalaman));
                },
                get barisAwal() {
                    return this.totalData === 0 ? 0 : (this.halamanSekarang - 1) * this.barisPerHalaman + 1;
                },
                get barisAkhir() {
                    return Math.min(this.totalData, this.halamanSekarang * this.barisPerHalaman);
                },
                keHalamanPertama() {
                    this.halamanSekarang = 1;
                },
                keHalamanSebelumnya() {
                    if (this.halamanSekarang > 1) {
                        this.halamanSekarang--;
                    }
                },
                keHalamanSelanjutnya() {
                    if (this.halamanSekarang < this.totalHalaman) {
                        this.halamanSekarang++;
                    }
                },
                keHalamanTerakhir() {
                    this.halamanSekarang = this.totalHalaman;
                },
                gantiBarisPerHalaman(jumlah) {
                    this.barisPerHalaman = parseInt(jumlah);
                    this.halamanSekarang = 1;
                },
                apakahBarisTampil(index) {
                    return index >= (this.halamanSekarang - 1) * this.barisPerHalaman && index < this.halamanSekarang * this.barisPerHalaman;
                },

                // -----------------------------------------------------------------
                // FITUR MULTI-SELECT / PILIHAN GANDA BARIS TABEL (BULK SELECTION)
                // -----------------------------------------------------------------
                apakahTerpilih(id) {
                    return this.daftarTerpilih.includes(String(id));
                },
                togglePilih(id) {
                    const strId = String(id);
                    const idx = this.daftarTerpilih.indexOf(strId);
                    if (idx > -1) {
                        this.daftarTerpilih.splice(idx, 1);
                    } else {
                        this.daftarTerpilih.push(strId);
                    }
                },
                apakahSemuaTerpilih(daftarSemuaId = []) {
                    if (!daftarSemuaId || daftarSemuaId.length === 0) return false;
                    return daftarSemuaId.every(id => this.daftarTerpilih.includes(String(id)));
                },
                togglePilihSemua(daftarSemuaId = []) {
                    if (this.apakahSemuaTerpilih(daftarSemuaId)) {
                        const targetList = daftarSemuaId.map(String);
                        this.daftarTerpilih = this.daftarTerpilih.filter(id => !targetList.includes(id));
                    } else {
                        daftarSemuaId.forEach(id => {
                            const strId = String(id);
                            if (!this.daftarTerpilih.includes(strId)) {
                                this.daftarTerpilih.push(strId);
                            }
                        });
                    }
                },
                kosongkanPilihan() {
                    this.daftarTerpilih = [];
                    this.modalHapusMassalTerbuka = false;
                },
                salinTerpilih(pemisah = ', ') {
                    if (this.daftarTerpilih.length === 0) return;
                    const teks = this.daftarTerpilih.join(pemisah);
                    navigator.clipboard.writeText(teks);
                },
                modalHapusMassalTerbuka: false,
                bukaModalHapusMassal() {
                    if (this.daftarTerpilih.length > 0) {
                        this.modalHapusMassalTerbuka = true;
                    }
                },
                tutupModalHapusMassal() {
                    this.modalHapusMassalTerbuka = false;
                }
            };
        }

        // =========================================================================
        // HELPER GLOBAL: KOMPONEN DROPDOWN & INPUT RUPIAH KUSTOM (ALPINE.JS)
        // =========================================================================
        function dapatkanNilaiScope(konteks, path) {
            if (!path) return undefined;
            try {
                return new Function('with(this) { return ' + path + '; }').call(konteks);
            } catch(e) {
                return undefined;
            }
        }

        function tetapkanNilaiScope(konteks, path, nilai) {
            if (!path) return;
            try {
                new Function('val', 'with(this) { ' + path + ' = val; }').call(konteks, nilai);
            } catch(e) {}
        }

        function komponenDropdownKustom(config = {}) {
            return {
                buka: false,
                terpilih: (config.nilaiAwal !== undefined && config.nilaiAwal !== null) ? String(config.nilaiAwal) : '',
                labelTerpilih: '',
                daftar: Array.isArray(config.daftar) ? config.daftar : [],
                submitOtomatis: Boolean(config.submitOnChange),
                init() {
                    if (config.modelBind) {
                        const valAwal = dapatkanNilaiScope(this, config.modelBind);
                        if (valAwal !== undefined && valAwal !== null && valAwal !== '') {
                            this.terpilih = String(valAwal);
                        }
                        try {
                            this.$watch(config.modelBind, (val) => {
                                this.terpilih = (val !== undefined && val !== null) ? String(val) : '';
                                this.sinkronkanLabel();
                            });
                        } catch(e) {}
                    }
                    this.sinkronkanLabel();
                },
                sinkronkanLabel() {
                    if (this.terpilih !== null && this.terpilih !== '' && this.terpilih !== undefined) {
                        const item = this.daftar.find(d => String(d.nilai) === String(this.terpilih));
                        this.labelTerpilih = item ? item.label : this.terpilih;
                    } else {
                        this.labelTerpilih = '';
                    }
                },
                pilihItem(nilai, label) {
                    this.terpilih = (nilai !== undefined && nilai !== null) ? String(nilai) : '';
                    this.labelTerpilih = label || this.terpilih;
                    if (config.modelBind) {
                        tetapkanNilaiScope(this, config.modelBind, nilai);
                    }
                    this.buka = false;
                    this.$dispatch('input', nilai);
                    this.$dispatch('change', nilai);
                    if (this.submitOtomatis) {
                        this.$nextTick(() => {
                            if (this.$el && this.$el.closest('form')) {
                                const form = this.$el.closest('form');
                                if (typeof form.requestSubmit === 'function') {
                                    form.requestSubmit();
                                } else {
                                    form.submit();
                                }
                            }
                        });
                    }
                }
            };
        }

        function komponenInputRupiah(config = {}) {
            return {
                nilaiMurni: (config.nilaiAwal !== undefined && config.nilaiAwal !== null) ? config.nilaiAwal : '',
                nilaiTampil: '',
                init() {
                    this.formatKeTampilan(this.nilaiMurni);
                    if (config.modelBind) {
                        const valAwal = dapatkanNilaiScope(this, config.modelBind);
                        if (valAwal !== undefined && valAwal !== null && valAwal !== '') {
                            this.nilaiMurni = valAwal;
                            this.formatKeTampilan(this.nilaiMurni);
                        }
                        try {
                            this.$watch(config.modelBind, (val) => {
                                if (String(val) !== String(this.nilaiMurni)) {
                                    this.nilaiMurni = (val !== null && val !== undefined) ? val : '';
                                    this.formatKeTampilan(this.nilaiMurni);
                                }
                            });
                        } catch(e) {}
                    }
                },
                formatKeTampilan(angka) {
                    if (angka === '' || angka === null || angka === undefined) {
                        this.nilaiTampil = '';
                        this.nilaiMurni = '';
                        return;
                    }
                    let strAngka = String(angka).trim();
                    if (typeof angka === 'number') {
                        strAngka = Math.round(angka).toString();
                    } else if (strAngka.includes('.')) {
                        const bagianTitik = strAngka.split('.');
                        if (bagianTitik.length === 2 && bagianTitik[1].length <= 2) {
                            strAngka = Math.round(parseFloat(strAngka) || 0).toString();
                        }
                    }
                    const bersih = strAngka.replace(/[^0-9]/g, '');
                    if (!bersih) {
                        this.nilaiTampil = '';
                        this.nilaiMurni = '';
                        return;
                    }
                    const num = parseInt(bersih, 10);
                    this.nilaiMurni = num;
                    this.nilaiTampil = num.toLocaleString('id-ID');
                },
                ketikInput(e) {
                    if (config.readonly || config.disabled) return;
                    const inputVal = e.target.value;
                    const bersih = inputVal.replace(/[^0-9]/g, '');
                    if (!bersih) {
                        this.nilaiMurni = '';
                        this.nilaiTampil = '';
                        if (config.modelBind) {
                            tetapkanNilaiScope(this, config.modelBind, 0);
                        }
                        this.$dispatch('input', 0);
                        this.$dispatch('change', 0);
                        return;
                    }
                    const num = parseInt(bersih, 10);
                    this.nilaiMurni = num;
                    this.nilaiTampil = num.toLocaleString('id-ID');
                    if (config.modelBind) {
                        tetapkanNilaiScope(this, config.modelBind, num);
                    }
                    this.$dispatch('input', num);
                    this.$dispatch('change', num);
                }
            };
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', pulihkanPosisiSidebar);
        } else {
            pulihkanPosisiSidebar();
        }
    </script>

    @stack('skrip_tambahan')
</body>
</html>
