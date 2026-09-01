<header class="h-16 bg-slate-900 border-b border-slate-800 px-6 flex items-center justify-between shrink-0">
    <div class="text-sm font-medium text-slate-300">
        Portal Sistem Informasi Akuntansi & Distribusi
    </div>

    <div class="flex items-center gap-4">
        <span class="text-xs bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full border border-emerald-500/20 font-mono">
            Sesi Aktif
        </span>
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 font-medium">
                Keluar
            </button>
        </form>
    </div>
</header>
