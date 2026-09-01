<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        tampilkanSandi: false
      }"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Akuntansi & Distribusi Semen Terpadu</title>
    <meta name="description" content="Portal autentikasi Sistem Informasi Akuntansi & Distribusi Semen Terpadu">

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
              raised: { dark: '#1C1E2A' },
              border: { subtle: '#EEF0F4', dark: '#252837' },
            }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important} body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] flex items-center justify-center p-4 antialiased transition-colors duration-200">

  <div class="w-full max-w-5xl flex min-h-[600px] overflow-hidden rounded-xl shadow-xl border border-slate-200/60 dark:border-[#252837]">

    <!-- ============================================================
         PANEL KIRI — Branding & Statistik Sistem
         38% lebar, gelap solid, konten vertikal
    ============================================================ -->
    <div class="hidden md:flex md:w-[38%] bg-[#0C0E14] flex-col justify-between p-8 relative overflow-hidden">

      <!-- Aksen warna latar -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-blue-600/8 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
      <div class="absolute bottom-0 right-0 w-48 h-48 bg-indigo-600/6 rounded-full blur-2xl translate-x-1/4 translate-y-1/4 pointer-events-none"></div>

      <!-- Logo & Identitas -->
      <div class="relative z-10">
        <div class="flex items-center gap-2.5 mb-10">
          <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center shadow-md shadow-blue-600/30 shrink-0">
            <svg class="w-4.5 h-4.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
          </div>
          <div>
            <div class="text-sm font-bold text-white leading-none">PT Semen Indo</div>
            <div class="text-[11px] text-slate-500 mt-0.5">Distribusi & Akuntansi</div>
          </div>
        </div>

        <h1 class="text-2xl font-bold text-white leading-tight tracking-tight">
          Kontrol Operasional<br>Distribusi Semen
        </h1>
        <p class="mt-3 text-sm text-slate-400 leading-relaxed">
          Platform terintegrasi — mulai dari pesanan SO, pengiriman armada, stock opname gudang, hingga laporan laba rugi real-time.
        </p>
      </div>

      <!-- 4 Kartu Statistik Sistem -->
      <div class="relative z-10 space-y-2.5">
        <div class="text-[10px] font-semibold text-slate-600 uppercase tracking-widest mb-3">Status Sistem Hari Ini</div>

        <div class="grid grid-cols-2 gap-2">
          <!-- Kartu 1 -->
          <div class="bg-white/4 border border-white/8 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 mb-1">SO Aktif</div>
            <div class="text-xl font-bold font-mono text-white">48</div>
            <div class="text-[10px] text-emerald-400 mt-0.5">+6 hari ini</div>
          </div>
          <!-- Kartu 2 -->
          <div class="bg-white/4 border border-white/8 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 mb-1">Armada Jalan</div>
            <div class="text-xl font-bold font-mono text-white">8</div>
            <div class="text-[10px] text-sky-400 mt-0.5">Dari 12 unit</div>
          </div>
          <!-- Kartu 3 -->
          <div class="bg-white/4 border border-white/8 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 mb-1">Piutang AR</div>
            <div class="text-xl font-bold font-mono text-amber-400">128 Jt</div>
            <div class="text-[10px] text-slate-500 mt-0.5">14 faktur</div>
          </div>
          <!-- Kartu 4 -->
          <div class="bg-white/4 border border-white/8 rounded-xl p-3.5">
            <div class="text-[11px] text-slate-500 mb-1">Stok Semen</div>
            <div class="text-xl font-bold font-mono text-white">14.250</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Zak tersedia</div>
          </div>
        </div>

        <div class="mt-3 text-[11px] text-slate-600">
          &copy; {{ date('Y') }} PT Semen Indo. Hak cipta dilindungi.
        </div>
      </div>
    </div>

    <!-- ============================================================
         PANEL KANAN — Form Login Enterprise
         62% lebar, background terang/gelap
    ============================================================ -->
    <div class="flex-1 bg-white dark:bg-[#14161F] flex flex-col">

      <!-- Topbar Panel Kanan -->
      <div class="flex items-center justify-between px-8 pt-6 pb-2">
        <!-- Logo Mobile (hanya muncul di layar kecil) -->
        <div class="flex md:hidden items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
            </svg>
          </div>
          <span class="text-sm font-bold text-slate-900 dark:text-white">PT Semen Indo</span>
        </div>
        <div class="hidden md:block"></div>

        <!-- Toggle Tema -->
        <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#252837] transition-colors"
                :title="modeGelap ? 'Mode Terang' : 'Mode Gelap'">
          <svg x-show="modeGelap" class="w-4.5 h-4.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <svg x-show="!modeGelap" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
          </svg>
        </button>
      </div>

      <!-- Form Area -->
      <div class="flex-1 flex flex-col justify-center px-8 pb-8 pt-4">
        <div class="max-w-sm w-full mx-auto">

          <div class="mb-7">
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Selamat Datang</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Masuk ke ruang kerja Anda.</p>
          </div>

          <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">

            <!-- Input Username -->
            <div>
              <label for="nama_pengguna" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                Nama Pengguna
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
                <input type="text"
                       id="nama_pengguna"
                       name="nama_pengguna"
                       autocomplete="username"
                       placeholder="username Anda"
                       class="w-full pl-9 pr-3 py-2 text-sm rounded-lg
                              bg-[#F4F6F9] dark:bg-[#1C1E2A]
                              border border-[#E2E8F0] dark:border-[#252837]
                              text-slate-900 dark:text-slate-100
                              placeholder-slate-400 dark:placeholder-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500
                              transition-all">
              </div>
            </div>

            <!-- Input Kata Sandi -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label for="kata_sandi" class="text-xs font-semibold text-slate-600 dark:text-slate-400">Kata Sandi</label>
                <a href="#" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lupa sandi?</a>
              </div>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input :type="tampilkanSandi ? 'text' : 'password'"
                       id="kata_sandi"
                       name="kata_sandi"
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full pl-9 pr-10 py-2 text-sm rounded-lg
                              bg-[#F4F6F9] dark:bg-[#1C1E2A]
                              border border-[#E2E8F0] dark:border-[#252837]
                              text-slate-900 dark:text-slate-100
                              placeholder-slate-400 dark:placeholder-slate-600
                              focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500
                              transition-all">
                <button type="button"
                        @click="tampilkanSandi = !tampilkanSandi"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                  <svg x-show="!tampilkanSandi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  <svg x-show="tampilkanSandi" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Ingat Saya -->
            <div class="flex items-center gap-2">
              <input id="ingat_saya" type="checkbox"
                     class="w-3.5 h-3.5 rounded border-slate-300 dark:border-slate-600 bg-[#F4F6F9] dark:bg-[#1C1E2A] text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
              <label for="ingat_saya" class="text-xs text-slate-500 dark:text-slate-400 cursor-pointer">
                Ingat sesi ini selama 30 hari
              </label>
            </div>

            <!-- Tombol Masuk -->
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4
                           text-sm font-semibold text-white
                           bg-blue-600 hover:bg-blue-700 active:scale-[0.99]
                           rounded-lg shadow-sm shadow-blue-600/20
                           focus:outline-none focus:ring-2 focus:ring-blue-500/40
                           transition-all duration-150">
              Masuk ke Dashboard
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
              </svg>
            </button>
          </form>

          <!-- Akun Demo -->
          <div class="mt-7 pt-5 border-t border-[#EEF0F4] dark:border-[#252837]">
            <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-2">Akun Demo</div>
            <div class="flex flex-wrap gap-1.5">
              <span class="px-2 py-1 rounded bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400">superadmin</span>
              <span class="px-2 py-1 rounded bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400">spv_keuangan</span>
              <span class="px-2 py-1 rounded bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400">staff_ar</span>
              <span class="px-2 py-1 rounded bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400">direktur</span>
              <span class="px-2 py-1 rounded bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400">dispatcher</span>
            </div>
          </div>

        </div>
      </div>
    </div><!-- /panel kanan -->

  </div><!-- /kontainer split screen -->

</body>
</html>
