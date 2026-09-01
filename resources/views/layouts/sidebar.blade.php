<aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0">
    <div class="h-16 flex items-center px-6 border-b border-slate-800 font-bold text-lg text-emerald-400">
        SIAK Semen Terpadu
    </div>
    
    <nav class="flex-1 overflow-y-auto p-4 space-y-6 text-sm">
        {{-- Navigation Group: Utama --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Utama</div>
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white">
                Dashboard
            </a>
        </div>

        {{-- Navigation Group: Master Data --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Master Data</div>
            <div class="space-y-1">
                <a href="{{ route('master.customer.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Data Customer</a>
                <a href="{{ route('master.barang.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Data Barang / Semen</a>
                <a href="{{ route('master.wilayah.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Data Wilayah</a>
                <a href="{{ route('master.karyawan.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Data Karyawan</a>
            </div>
        </div>

        {{-- Navigation Group: Keuangan --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keuangan & Akuntansi</div>
            <div class="space-y-1">
                <a href="{{ route('keuangan.ar.faktur') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">AR: Faktur Penjualan</a>
                <a href="{{ route('keuangan.ar.piutang') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">AR: List Piutang</a>
                <a href="{{ route('keuangan.ar.deposit') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">AR: Deposit Customer</a>
                <a href="{{ route('keuangan.ap.pembelian_so') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">AP: Pembelian SO</a>
                <a href="{{ route('keuangan.ap.pengeluaran') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">AP: Pengeluaran Kas</a>
                <a href="{{ route('keuangan.akuntansi.kode_akun') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Akuntansi: Kode Akun (COA)</a>
                <a href="{{ route('keuangan.akuntansi.jurnal') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Akuntansi: Jurnal Umum</a>
            </div>
        </div>

        {{-- Navigation Group: Operasional --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Operasional & Logistik</div>
            <div class="space-y-1">
                <a href="{{ route('operasional.gudang.stok') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Gudang & Mutasi Stok</a>
                <a href="{{ route('operasional.gudang.opname') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Stock Opname</a>
                <a href="{{ route('operasional.armada.kendaraan') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Armada Truk</a>
                <a href="{{ route('operasional.armada.driver') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Data Driver</a>
                <a href="{{ route('operasional.pengiriman.surat_jalan') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Surat Jalan Dispatcher</a>
                <a href="{{ route('operasional.bengkel.perbaikan') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Bengkel & Pemeliharaan</a>
            </div>
        </div>

        {{-- Navigation Group: Laporan Eksekutif --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Laporan Eksekutif</div>
            <div class="space-y-1">
                <a href="{{ route('laporan.neraca') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Laporan Neraca</a>
                <a href="{{ route('laporan.laba_rugi') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Laporan Laba Rugi</a>
                <a href="{{ route('laporan.arus_kas') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:bg-slate-800">Laporan Arus Kas</a>
            </div>
        </div>
    </nav>
</aside>
