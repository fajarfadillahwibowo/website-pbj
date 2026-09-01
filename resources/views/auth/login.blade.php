<!DOCTYPE html>
<html lang="id"
      x-data="{
        modeGelap: localStorage.getItem('tema') === 'gelap',
        tampilkanSandi: false,
        inputUsername: '{{ old('nama_pengguna') }}',
        inputPassword: '',
        isiDemo(usr) {
          this.inputUsername = usr;
          this.inputPassword = 'password123';
        }
      }"
      :class="{ 'dark': modeGelap }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Akuntansi & Distribusi Semen</title>
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
              border: { subtle: '#EEF0F4', dark: '#252837' },
            }
          }
        }
      }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important} body{font-family:'Inter',sans-serif}</style>
</head>
<body class="min-h-screen bg-[#F4F6F9] dark:bg-[#0C0E14] flex flex-col items-center justify-center p-4 antialiased transition-colors duration-200 relative">

  <!-- Sakelar Tema Terang/Gelap (Pojok Kanan Atas Layar) -->
  <div class="absolute top-4 right-4 sm:top-6 sm:right-6">
    <button @click="modeGelap = !modeGelap; localStorage.setItem('tema', modeGelap ? 'gelap' : 'terang')"
            class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white bg-white dark:bg-[#14161F] border border-[#E2E8F0] dark:border-[#252837] shadow-sm transition-colors"
            :title="modeGelap ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'">
      <svg x-show="modeGelap" class="w-4.5 h-4.5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
      <svg x-show="!modeGelap" class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
      </svg>
    </button>
  </div>

  <!-- ============================================================
       KARTU FORM LOGIN ELEGAN (CENTERED CARD)
  ============================================================ -->
  <div class="w-full max-w-md bg-white dark:bg-[#14161F] rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-[#252837] p-7 sm:p-9 transition-all">
    
    <!-- Header Kartu: Logo & Identitas Perusahaan -->
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-600/20 shrink-0">
        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
      </div>
      <div>
        <div class="text-sm font-bold text-slate-900 dark:text-slate-100 leading-tight">PT Pura Balkom Jaya</div>
        <div class="text-xs text-slate-400 font-medium">Distribusi & Akuntansi</div>
      </div>
    </div>

    <!-- Judul Form -->
    <div class="mb-6">
      <h1 class="text-xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">Selamat Datang</h1>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Masukkan kredensial akun untuk mengakses ruang kerja Anda.</p>
    </div>

    <!-- Pesan Kesalahan Validasi Login -->
    @if ($errors->any())
      <div class="mb-5 p-3.5 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-xs text-red-600 dark:text-red-400 flex items-start gap-2.5">
        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Form Input Autentikasi -->
    <form action="{{ route('auth.proses_login') }}" method="POST" class="space-y-4">
      @csrf
      
      <!-- Input Nama Pengguna -->
      <div>
        <label for="nama_pengguna" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
          Nama Pengguna
        </label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                 placeholder="Contoh: spv_keuangan"
                 class="w-full pl-10 pr-3.5 py-2.5 text-sm rounded-xl
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
          <label for="kata_sandi" class="text-xs font-semibold text-slate-700 dark:text-slate-300">Kata Sandi</label>
          <a href="#" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lupa sandi?</a>
        </div>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
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
                 class="w-full pl-10 pr-10 py-2.5 text-sm rounded-xl
                        bg-[#F4F6F9] dark:bg-[#1C1E2A]
                        border border-[#E2E8F0] dark:border-[#252837]
                        text-slate-900 dark:text-slate-100
                        placeholder-slate-400 dark:placeholder-slate-600
                        focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500
                        transition-all">
          
          <!-- Tombol Intip Password -->
          <button type="button"
                  @click="tampilkanSandi = !tampilkanSandi"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
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

      <!-- Opsi Sesi & Tombol Submit -->
      <div class="flex items-center justify-between">
        <label class="inline-flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="ingat_saya" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20">
          <span class="text-xs text-slate-600 dark:text-slate-400">Ingat sesi saya (30 hari)</span>
        </label>
      </div>

      <button type="submit"
              class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-blue-600/25 transition-all flex items-center justify-center gap-2">
        <span>Masuk ke Dashboard</span>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </button>
    </form>

    <!-- Daftar Akun Demo Cepat -->
    <div class="mt-6 pt-5 border-t border-[#EEF0F4] dark:border-[#252837]">
      <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-widest mb-2.5">Klik Akun Demo Masuk (Sandi: password123):</div>
      <div class="flex flex-wrap gap-1.5">
        <button type="button" @click="isiDemo('superadmin')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-500/10 font-medium transition-all">1. superadmin</button>
        <button type="button" @click="isiDemo('spv_keuangan')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">2. spv_keuangan</button>
        <button type="button" @click="isiDemo('staff_ar')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">3. staff_ar</button>
        <button type="button" @click="isiDemo('staff_ap')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">4. staff_ap</button>
        <button type="button" @click="isiDemo('dispatcher')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">5. dispatcher</button>
        <button type="button" @click="isiDemo('pengawas_driver')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">6. pengawas_driver</button>
        <button type="button" @click="isiDemo('spv_gudang')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">7. spv_gudang</button>
        <button type="button" @click="isiDemo('direktur')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">8. direktur</button>
        <button type="button" @click="isiDemo('spv_operasional')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">9. spv_operasional</button>
        <button type="button" @click="isiDemo('pengawas_kendaraan')" class="px-2 py-1 rounded-md bg-[#F4F6F9] dark:bg-[#1C1E2A] border border-[#E2E8F0] dark:border-[#252837] text-[11px] font-mono text-slate-600 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-600 font-medium transition-all">10. pengawas_kendaraan</button>
      </div>
    </div>

  </div>

  <!-- Footer Bawah -->
  <div class="mt-6 text-center text-xs text-slate-400">
    &copy; {{ date('Y') }} PT Pura Balkom Jaya. Hak cipta dilindungi.
  </div>

</body>
</html>
