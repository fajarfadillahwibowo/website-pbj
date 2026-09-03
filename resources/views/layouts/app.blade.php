<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        sidebarTerlipat: false,
        kunciRbac: true,
        dropdownRoleTerbuka: false,
        jabatanAktif: (function() {
            try {
                const tersimpan = localStorage.getItem('jabatan_aktif');
                if (tersimpan && tersimpan !== 'null' && tersimpan !== 'undefined') {
                    return tersimpan;
                }
            } catch (e) {}
            return '{{ session('kode_jabatan', 'SUPER_ADMIN') }}';
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
        this.$nextTick(() => {
          if (typeof pulihkanPosisiSidebar === 'function') {
            pulihkanPosisiSidebar();
          }
        });
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

    {{-- Indikator Loading Bar Halus (YouTube / GitHub Enterprise Style) --}}
    <div id="indikatorLoadingHalaman" 
         class="fixed top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-500 via-indigo-500 to-emerald-500 z-50 transition-all duration-300 pointer-events-none opacity-0" 
         style="width: 0%;"></div>

    {{-- Sidebar Navigasi Menyeluruh --}}
    @include('layouts.sidebar')

    {{-- Kontainer Konten Utama --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header Topbar Enterprise --}}
        @include('layouts.header')

        {{-- Area Konten Dinamis (Hanya bagian ini yang diperbarui saat klik menu sidebar) --}}
        <main id="kontenUtama" class="flex-1 overflow-y-auto p-5 sm:p-6 bg-[#F4F6F9] dark:bg-[#0C0E14] transition-opacity duration-150">
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

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', pulihkanPosisiSidebar);
        } else {
            pulihkanPosisiSidebar();
        }
    </script>

    @stack('skrip_tambahan')
</body>
</html>
