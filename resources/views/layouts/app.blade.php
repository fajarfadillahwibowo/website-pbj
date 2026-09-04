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
                const roleDariSesi = '{{ session('kode_jabatan') }}';
                try {
                    localStorage.setItem('jabatan_aktif', roleDariSesi);
                } catch(e) {}
                return roleDariSesi;
            @else
                try {
                    const tersimpan = localStorage.getItem('jabatan_aktif');
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
        // Deteksi ukuran layar untuk initial state sidebar
        const initSidebarState = () => {
          const lebarLayar = window.innerWidth;
          if (lebarLayar < 768) {
            // Mobile: sidebar tersembunyi (drawer)
            sidebarMobileTerbuka = false;
            sidebarTerlipat = true;
          } else if (lebarLayar < 1024) {
            // Tablet: sidebar collapsed (icon-only)
            sidebarTerlipat = true;
          } else {
            // Desktop: ambil dari preferensi tersimpan
            const tersimpan = localStorage.getItem('sidebar_terlipat');
            if (tersimpan !== null) {
              sidebarTerlipat = tersimpan === 'true';
            } else {
              sidebarTerlipat = false;
            }
          }
        };
        initSidebarState();

        // Auto-tutup drawer mobile saat resize ke desktop
        const handleResize = () => {
          if (window.innerWidth >= 768) {
            sidebarMobileTerbuka = false;
            document.body.style.overflow = '';
          }
          if (window.innerWidth >= 1024) {
            const tersimpan = localStorage.getItem('sidebar_terlipat');
            if (tersimpan !== null) {
              sidebarTerlipat = tersimpan === 'true';
            } else {
              sidebarTerlipat = false;
            }
          } else if (window.innerWidth >= 768) {
            sidebarTerlipat = true;
          }
        };
        window.addEventListener('resize', handleResize, { passive: true });

        $watch('jabatanAktif', v => {
          try { localStorage.setItem('jabatan_aktif', v); } catch(e) {}
          fetch('{{ route("api.sinkronisasi_role") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ kode_jabatan: v })
          }).catch(() => {});
        });
        $watch('sidebarTerlipat', v => {
          // Hanya simpan preferensi di desktop
          if (window.innerWidth >= 1024) {
            try { localStorage.setItem('sidebar_terlipat', v ? 'true' : 'false'); } catch(e) {}
          }
        });
        $watch('sidebarMobileTerbuka', v => {
          document.body.style.overflow = v ? 'hidden' : '';
        });
        this.$nextTick(() => {
          if (typeof pulihkanPosisiSidebar === 'function') {
            pulihkanPosisiSidebar();
          }
        });
      "
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
        #indikatorLoadingHalaman {
          display: none !important;
        }
        main {
          padding: 0 !important;
          overflow: visible !important;
        }
      }
    </style>
    @stack('gaya_tambahan')
</head>
<body class="h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] text-slate-900 dark:text-slate-100 antialiased flex overflow-hidden transition-colors duration-200" style="min-height: 100dvh; min-height: 100vh;">

    {{-- Indikator Loading Bar Halus (YouTube / GitHub Enterprise Style) --}}
    <div id="indikatorLoadingHalaman" 
         class="fixed top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-500 z-50 transition-all duration-300 pointer-events-none opacity-0" 
         style="width: 0%;"></div>

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
                    if (script.src) {
                        const s = document.createElement('script');
                        s.src = script.src;
                        s.async = false;
                        document.head.appendChild(s);
                    } else if (script.textContent.trim()) {
                        window.eval(script.textContent);
                    }
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

        // Mesin Pemutakhiran Konten Parsial (SPA Swap)
        async function muatKontenDinamis(url, targetLink = null, pushKeHistory = true) {
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

            mulaiIndikatorLoading();
            kontenUtama.style.opacity = '0.5';

            try {
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

                const html = await response.text();
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
                if (pushKeHistory) {
                    window.history.pushState({ url: url }, judulBaru, url);
                }

                // 4. Sinkronkan status aktif sidebar secara visual (SIDEBAR TIDAK PERNAH DI-REFRESH / DI-RELOAD)
                const sidebarBaru = doc.getElementById('navigasiSidebar');
                sinkronkanSidebarAktif(sidebarBaru);

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

                // 8. Beri notifikasi global bahwa halaman telah berganti
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

        // Tangani form GET (Filter / Pencarian di dalam konten utama) agar tidak me-refresh sidebar
        document.addEventListener('submit', function(event) {
            const form = event.target;
            if (!form || !form.closest('#kontenUtama')) return;

            const method = (form.getAttribute('method') || 'GET').toUpperCase();
            if (method !== 'GET') return; // Form POST/PUT/DELETE tetap berjalan normal dengan token CSRF

            const action = form.getAttribute('action') || window.location.href;
            const url = new URL(action, window.location.origin);
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value !== '') {
                    params.append(key, value);
                }
            }
            url.search = params.toString();

            event.preventDefault();
            muatKontenDinamis(url.href);
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
                                this.$el.closest('form').submit();
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
