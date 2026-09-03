<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        tampilkanSandi: false,
        inputUsername: '{{ old('nama_pengguna') }}',
        inputPassword: '',
        roleTerpilih: '',
        pesanNotif: '',
        isiDemo(usr, label) {
          this.inputUsername = usr;
          this.inputPassword = 'password123';
          this.roleTerpilih = usr;
          this.pesanNotif = 'Akun demo ' + label + ' berhasil diisi!';
          setTimeout(() => { this.pesanNotif = ''; }, 3000);
        }
      }"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Masuk — PT Putra Balkom Jaya (Distribusi Semen & Akuntansi)</title>
    <meta name="description" content="Sistem Informasi Akuntansi Manajemen & Logistik Distribusi Semen PT Putra Balkom Jaya">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
              display: ['Outfit', 'sans-serif'],
              mono: ['"JetBrains Mono"', 'monospace']
            },
            colors: {
              brand: {
                50: '#EFF6FF',
                100: '#DBEAFE',
                500: '#3B82F6',
                600: '#2563EB',
                700: '#1D4ED8',
                800: '#1E40AF',
                900: '#1E3A8A',
              },
              dark: {
                bg: '#0A0D14',
                surface: '#111522',
                card: '#161B2C',
                border: '#23293D',
              }
            }
          }
        }
      }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
      [x-cloak] { display: none !important; }
      body { font-family: 'Inter', sans-serif; }
      .font-display { font-family: 'Outfit', sans-serif; }
      
      /* Subtle custom scrollbar */
      ::-webkit-scrollbar { width: 6px; height: 6px; }
      ::-webkit-scrollbar-track { background: transparent; }
      ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 9999px; }
      ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.5); }
      
      /* Dot pattern */
      .bg-dot-pattern {
        background-image: radial-gradient(rgba(100, 116, 139, 0.15) 1px, transparent 1px);
        background-size: 20px 20px;
      }
      .dark .bg-dot-pattern {
        background-image: radial-gradient(rgba(148, 163, 184, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
      }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-[#0A0D14] text-slate-900 dark:text-slate-100 antialiased transition-colors duration-200">

  <!-- ============================================================
       CONTAINER UTAMA: SPLIT SCREEN MODERN ENTERPRISE
  ============================================================ -->
  <div class="min-h-screen grid lg:grid-cols-12 overflow-hidden">

    <!-- ============================================================
         PANEL KIRI (DESKTOP): HERO SHOWCASE TEMA DISTRIBUSI SEMEN
    ============================================================ -->
    <div class="hidden lg:flex lg:col-span-6 xl:col-span-7 flex-col justify-between relative p-10 xl:p-14 overflow-hidden bg-slate-950 text-white select-none">
      
      <!-- Backdrop Image (Industrial Cement Logistics Hub) -->
      <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/cement-distribution-hub.jpg') }}" 
             alt="Hub Distribusi Semen PT Putra Balkom Jaya"
             class="w-full h-full object-cover object-center scale-105 transform hover:scale-100 transition-transform duration-1000">
        
        <!-- Gradient Overlay Multi-Layer (Twilight Cinematic) -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#0A0D14] via-[#0A0D14]/80 to-[#0A0D14]/50"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0A0D14]/90 via-[#0A0D14]/60 to-transparent"></div>
        
        <!-- Ambient Glowing Orbs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>
      </div>

      <!-- KONTEN HERO ATAS: Status & Identitas -->
      <div class="relative z-10">
        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-medium text-slate-200 shadow-lg">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          <span class="tracking-wide">Sistem Operasional Distribusi & Keuangan Aktif</span>
        </div>
      </div>

      <!-- KONTEN HERO TENGAH: Value Proposition & Pilar Sistem -->
      <div class="relative z-10 max-w-xl my-auto py-12">
        <div class="inline-flex items-center gap-2 text-xs font-mono font-bold tracking-widest text-blue-400 uppercase mb-3">
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          Enterprise Logistics & Financial ERP
        </div>

        <h2 class="text-3xl xl:text-4xl font-extrabold font-display leading-tight tracking-tight text-white mb-4">
          Ekosistem Terpadu Distribusi Semen & Akuntansi Logistik
        </h2>

        <p class="text-sm xl:text-base text-slate-300 leading-relaxed font-light mb-8">
          Platform terpadu untuk monitoring armada ekspedisi, pengelolaan Surat Jalan, mutasi buffer gudang semen, hingga otomatisasi pembukuan dan laporan keuangan laba rugi berstandar PSAK.
        </p>

        <!-- 3 Feature Glass Cards -->
        <div class="grid sm:grid-cols-3 gap-3.5">
          <!-- Card 1: Logistik Armada -->
          <div class="p-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 hover:border-white/20 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 18V6a2 2 0 00-2-2H4a2 2 0 00-2 2v11a1 1 0 001 1h2"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 8h4.5a1.5 1.5 0 011.2.6l2.8 3.7c.3.4.5.9.5 1.4V17a1 1 0 01-1 1h-2"/>
                <circle cx="8" cy="18" r="2"/>
                <circle cx="18" cy="18" r="2"/>
              </svg>
            </div>
            <div class="text-xs font-bold text-white mb-1">Armada & SJ</div>
            <div class="text-[11px] text-slate-400 leading-snug">Tracking ritase truk, supir, & tarif rute OA presisi.</div>
          </div>

          <!-- Card 2: Stok Gudang -->
          <div class="p-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 hover:border-white/20 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
              </svg>
            </div>
            <div class="text-xs font-bold text-white mb-1">Gudang & SO</div>
            <div class="text-[11px] text-slate-400 leading-snug">Stok fisik semen zak & curah dengan stock opname live.</div>
          </div>

          <!-- Card 3: Auto Journal -->
          <div class="p-4 rounded-xl bg-white/10 backdrop-blur-md border border-white/10 hover:border-white/20 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center mb-2.5 group-hover:scale-110 transition-transform">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <div class="text-xs font-bold text-white mb-1">Jurnal Otomatis</div>
            <div class="text-[11px] text-slate-400 leading-snug">Sinkronisasi debit-kredit faktur & P&L instan.</div>
          </div>
        </div>
      </div>

      <!-- KONTEN HERO BAWAH: Trust & Compliance -->
      <div class="relative z-10 flex items-center justify-between pt-6 border-t border-white/10 text-xs text-slate-400">
        <div class="flex items-center gap-2">
          <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Enkripsi Sesi 256-Bit & Kontrol Akses 10 Jabatan</span>
        </div>
        <div class="font-mono text-[11px] text-slate-400">
          PT PUTRA BALKOM JAYA
        </div>
      </div>

    </div>

    <!-- ============================================================
         PANEL KANAN: FORMULIR AUTENTIKASI MASUK
    ============================================================ -->
    <div class="lg:col-span-6 xl:col-span-5 flex flex-col justify-between p-6 sm:p-10 xl:p-12 bg-white dark:bg-[#0E111A] bg-dot-pattern relative">
      
      <!-- Top Bar: Sakelar Tema & Versi -->
      <div class="flex items-center justify-between w-full mb-6 sm:mb-8">
        <!-- Badge Versi -->
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-[#161B2C] border border-slate-200/80 dark:border-slate-800 text-[11px] font-mono font-medium text-slate-600 dark:text-slate-400">
          <span class="w-1.5 h-1.5 rounded-full bg-blue-600 dark:bg-blue-400"></span>
          <span>v2.5 Enterprise</span>
        </div>

        <!-- Tombol Pengalih Mode Terang/Gelap -->
        <button type="button"
                @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-[#161B2C] border border-slate-200 dark:border-slate-800 hover:bg-slate-200/70 dark:hover:bg-slate-800 transition-all focus:outline-none"
                :title="modeGelap ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
          <svg x-show="modeGelap" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <svg x-show="!modeGelap" class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
        </button>
      </div>

      <!-- Area Inti Form Login -->
      <div class="w-full max-w-md mx-auto my-auto py-4">
        
        <!-- Header Identitas Brand Perusahaan -->
        <div class="mb-8">
          <div class="flex items-center gap-3.5 mb-5">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-white to-slate-100 dark:from-[#161B2C] dark:to-[#111522] p-2 border border-slate-200/80 dark:border-slate-700/60 shadow-md flex items-center justify-center shrink-0">
              <img src="{{ asset('images/logo-pbj.png') }}" 
                   alt="Logo PT Putra Balkom Jaya" 
                   class="w-full h-full object-contain drop-shadow-xs"
                   loading="eager">
            </div>
            <div>
              <div class="text-base font-extrabold font-display tracking-tight text-slate-900 dark:text-white leading-tight">
                PT Putra Balkom Jaya
              </div>
              <div class="inline-flex items-center gap-1.5 mt-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-2 py-0.5 rounded-md border border-blue-200/60 dark:border-blue-800/60">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                </svg>
                Distribusi & Logistik Semen
              </div>
            </div>
          </div>

          <h1 class="text-2xl sm:text-3xl font-bold font-display tracking-tight text-slate-900 dark:text-white">
            Portal Masuk Petugas
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1.5">
            Silakan masukkan kredensial akun untuk mengakses ruang kerja operasional dan akuntansi.
          </p>
        </div>

        <!-- Toast Notifikasi Pemilihan Akun Demo -->
        <div x-show="pesanNotif"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mb-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5 shadow-sm">
          <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          <span class="font-medium" x-text="pesanNotif"></span>
        </div>

        <!-- Pesan Error Validasi Login -->
        @if ($errors->any())
          <div class="mb-5 p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-xs text-rose-700 dark:text-rose-300 flex items-start gap-2.5 shadow-sm">
            <svg class="w-4 h-4 shrink-0 mt-0.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="space-y-0.5">
              @foreach ($errors->all() as $error)
                <div class="font-medium">{{ $error }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Formulir Input Autentikasi -->
        <form action="{{ route('auth.proses_login') }}" method="POST" class="space-y-4">
          @csrf
          
          <!-- Input Nama Pengguna -->
          <div>
            <label for="nama_pengguna" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
              Nama Pengguna (Username)
            </label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <input type="text"
                     id="nama_pengguna"
                     name="nama_pengguna"
                     x-model="inputUsername"
                     autocomplete="username"
                     required
                     placeholder="Masukkan nama pengguna..."
                     class="w-full pl-10 pr-4 py-2.5 sm:py-3 text-sm rounded-xl
                            bg-slate-50 dark:bg-[#161B2C]
                            border border-slate-300/80 dark:border-slate-800
                            text-slate-900 dark:text-white
                            placeholder-slate-400 dark:placeholder-slate-500
                            focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:focus:border-blue-400
                            transition-all duration-150">
            </div>
          </div>

          <!-- Input Kata Sandi -->
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="kata_sandi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kata Sandi</label>
              <a href="#" 
                 class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                Lupa sandi?
              </a>
            </div>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-blue-600 dark:group-focus-within:text-blue-400 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <input :type="tampilkanSandi ? 'text' : 'password'"
                     id="kata_sandi"
                     name="kata_sandi"
                     x-model="inputPassword"
                     autocomplete="current-password"
                     required
                     placeholder="••••••••"
                     class="w-full pl-10 pr-11 py-2.5 sm:py-3 text-sm rounded-xl
                            bg-slate-50 dark:bg-[#161B2C]
                            border border-slate-300/80 dark:border-slate-800
                            text-slate-900 dark:text-white
                            placeholder-slate-400 dark:placeholder-slate-500
                            focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 dark:focus:border-blue-400
                            transition-all duration-150">
              
              <!-- Tombol Intip / Sembunyikan Sandi -->
              <button type="button"
                      @click="tampilkanSandi = !tampilkanSandi"
                      class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none transition-colors"
                      :title="tampilkanSandi ? 'Sembunyikan sandi' : 'Tampilkan sandi'">
                <svg x-show="!tampilkanSandi" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="tampilkanSandi" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Opsi Ingat Sesi -->
          <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
              <input type="checkbox" 
                     name="ingat_saya" 
                     class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 text-blue-600 dark:bg-[#161B2C] focus:ring-blue-500/20">
              <span class="text-xs text-slate-600 dark:text-slate-400 font-medium">Ingat sesi login saya</span>
            </label>
          </div>

          <!-- Tombol Masuk Utama (Gradient Accent) -->
          <button type="submit"
                  class="w-full py-3 px-4 rounded-xl text-white font-semibold text-sm
                         bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-500 hover:to-indigo-600
                         active:scale-[0.99] shadow-lg shadow-blue-600/25 dark:shadow-blue-900/30
                         transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer">
            <span>Masuk ke Dashboard</span>
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </button>
        </form>

        <!-- ============================================================
             AKUN DEMO CEPAT (TERBAGI PER DIVISI)
        ============================================================ -->
        <div class="mt-7 pt-5 border-t border-slate-200/80 dark:border-slate-800">
          <div class="flex items-center justify-between mb-3">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
              Akses Cepat Akun Demo (Sandi: password123)
            </span>
            <span class="text-[10px] font-mono text-blue-600 dark:text-blue-400 font-semibold">10 Role</span>
          </div>

          <!-- Grup 1: Keuangan & Pimpinan -->
          <div class="mb-2.5">
            <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
              Divisi Keuangan & Eksekutif
            </div>
            <div class="flex flex-wrap gap-1.5">
              <button type="button" @click="isiDemo('spv_keuangan', 'SPV Keuangan')" 
                      :class="roleTerpilih === 'spv_keuangan' ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                spv_keuangan
              </button>
              <button type="button" @click="isiDemo('staff_ar', 'Staff AR')" 
                      :class="roleTerpilih === 'staff_ar' ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                staff_ar
              </button>
              <button type="button" @click="isiDemo('staff_ap', 'Staff AP')" 
                      :class="roleTerpilih === 'staff_ap' ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                staff_ap
              </button>
              <button type="button" @click="isiDemo('direktur', 'Direktur & Manager')" 
                      :class="roleTerpilih === 'direktur' ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 hover:text-emerald-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                direktur
              </button>
            </div>
          </div>

          <!-- Grup 2: Logistik & Operasional Lapangan -->
          <div class="mb-2.5">
            <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
              Divisi Operasional & Lapangan
            </div>
            <div class="flex flex-wrap gap-1.5">
              <button type="button" @click="isiDemo('dispatcher', 'Dispatcher')" 
                      :class="roleTerpilih === 'dispatcher' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                dispatcher
              </button>
              <button type="button" @click="isiDemo('spv_operasional', 'SPV Operasional')" 
                      :class="roleTerpilih === 'spv_operasional' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                spv_operasional
              </button>
              <button type="button" @click="isiDemo('spv_gudang', 'SPV Gudang')" 
                      :class="roleTerpilih === 'spv_gudang' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                spv_gudang
              </button>
              <button type="button" @click="isiDemo('pengawas_driver', 'Pengawas Driver')" 
                      :class="roleTerpilih === 'pengawas_driver' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                pengawas_driver
              </button>
              <button type="button" @click="isiDemo('pengawas_kendaraan', 'Pengawas Kendaraan')" 
                      :class="roleTerpilih === 'pengawas_kendaraan' ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-950/30 hover:text-blue-600'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                pengawas_kendaraan
              </button>
            </div>
          </div>

          <!-- Grup 3: Administrator Sistem -->
          <div>
            <div class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 mb-1 flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
              Kontrol Sistem
            </div>
            <div class="flex flex-wrap gap-1.5">
              <button type="button" @click="isiDemo('superadmin', 'Super Admin')" 
                      :class="roleTerpilih === 'superadmin' ? 'ring-2 ring-purple-500 bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-bold' : 'bg-slate-100 dark:bg-[#161B2C] text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/30'"
                      class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-800 text-[11px] font-mono transition-all">
                superadmin
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Bawah Hak Cipta -->
      <div class="pt-6 border-t border-slate-200/80 dark:border-slate-800/80 text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400 dark:text-slate-500">
        <div>
          &copy; {{ date('Y') }} <span class="font-semibold text-slate-600 dark:text-slate-400">PT Putra Balkom Jaya</span>. Hak cipta dilindungi.
        </div>
        <div class="flex items-center gap-3 text-[11px]">
          <span class="hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">Panduan Sistem</span>
          <span>&bull;</span>
          <span class="hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">Bantuan TI</span>
        </div>
      </div>

    </div>

  </div>

</body>
</html>
