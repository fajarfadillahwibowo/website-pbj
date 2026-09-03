<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $faktur->nomor_faktur }} - PT Putra Balkom Jaya</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono-angka {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .tombol-cetak {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .halaman-faktur {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 10mm !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen py-8 px-4 flex flex-col items-center">

    <!-- Tombol Tindakan Atas (Sembunyi saat cetak) -->
    <div class="tombol-cetak w-full max-w-4xl mb-4 flex items-center justify-between">
        <a href="{{ route('keuangan.ar.faktur') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-300 shadow-xs transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Faktur
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Dokumen Faktur (Print / PDF)
            </button>
        </div>
    </div>

    <!-- Lembar Kertas Faktur Resmi -->
    <div class="halaman-faktur bg-white w-full max-w-4xl p-8 sm:p-12 rounded-2xl shadow-xl border border-slate-200">
        
        <!-- Header / Kop Surat Resmi -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b-2 border-slate-800 gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-pbj.png') }}" alt="Logo PT Putra Balkom Jaya" class="w-16 h-16 object-contain" onerror="this.src='{{ asset('logo.png') }}'">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-950 uppercase">PT PUTRA BALKOM JAYA</h1>
                    <p class="text-xs font-semibold text-slate-600 tracking-wide">DISTRIBUSI SEMEN & LOGISTIK TERPADU</p>
                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                        Jl. Lintas Sumatera Km. 18, Palembang - Sumatera Selatan<br>
                        Telp: (0711) 561234 · Email: operasional@putrabalkomjaya.co.id
                    </p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <div class="inline-block px-3 py-1 bg-slate-900 text-white rounded-lg text-xs font-extrabold tracking-widest uppercase mb-1">
                    FAKTUR PENJUALAN
                </div>
                <div class="text-sm font-mono-angka font-bold text-indigo-700 tracking-wide mt-1">
                    {{ $faktur->nomor_faktur }}
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    Tanggal: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($faktur->tanggal_penjualan)->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Info Customer & Tujuan Pengiriman -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-200 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tagihan Ditujukan Kepada:</span>
                <h3 class="text-sm font-extrabold text-slate-900">{{ $faktur->customer->nama_pemilik ?? 'Customer Tunai' }}</h3>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $faktur->customer->nama_toko_bangunan ?? '-' }}</p>
                <p class="text-slate-500 mt-1 leading-relaxed">{{ $faktur->customer->alamat ?? 'Alamat tidak tercatat' }}</p>
                <p class="text-slate-600 font-mono-angka mt-1">No. HP / WA: {{ $faktur->customer->no_hp ?? '-' }}</p>
            </div>

            <div class="sm:border-l sm:border-slate-100 sm:pl-6">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tujuan Pengiriman & Ketentuan Pembayaran:</span>
                <div class="space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Cabang / Drop Point:</span>
                        <span class="font-semibold text-slate-800 text-right">{{ $faktur->tokoBangunan->nama_toko ?? ($faktur->customer->nama_toko_bangunan ?? 'Pusat') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Metode Pembayaran:</span>
                        <span class="font-bold px-2 py-0.5 rounded text-[11px] {{ $faktur->metode_pembayaran === 'Tunai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($faktur->metode_pembayaran === 'Transfer' ? 'bg-blue-50 text-blue-700 border border-blue-200' : ($faktur->metode_pembayaran === 'Potong Deposit' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-amber-50 text-amber-700 border border-amber-200')) }}">
                            {{ $faktur->metode_pembayaran }}
                        </span>
                    </div>
                    @if($faktur->metode_pembayaran === 'Kredit / Piutang')
                    <div class="flex justify-between">
                        <span class="text-slate-500">Jatuh Tempo:</span>
                        <span class="font-semibold text-rose-600 font-mono-angka">{{ $faktur->jatuh_tempo ? \Carbon\Carbon::parse($faktur->jatuh_tempo)->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status Faktur:</span>
                        <span class="font-extrabold uppercase {{ $faktur->status_pembayaran === 'Lunas' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $faktur->status_pembayaran }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rincian Komoditas Semen -->
        <div class="py-6">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-300 text-[11px] font-bold text-slate-700 uppercase">
                        <th class="py-3 px-2 w-10 text-center">No</th>
                        <th class="py-3 px-2">Deskripsi Produk Semen</th>
                        <th class="py-3 px-2 text-center w-24">Satuan</th>
                        <th class="py-3 px-2 text-right w-36">Harga Satuan</th>
                        <th class="py-3 px-2 text-right w-40">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3.5 px-2 text-center font-mono-angka text-slate-500">1</td>
                        <td class="py-3.5 px-2">
                            <span class="font-bold text-slate-900 block text-xs">Semen Portland Composite (PCC) SIG / Baturaja</span>
                            <span class="text-[11px] text-slate-500">Kemasan Zak 50 Kg SNI - Distribusi Resmi Wilayah Sumatera</span>
                        </td>
                        <td class="py-3.5 px-2 text-center font-semibold text-slate-600">Zak / Partai</td>
                        <td class="py-3.5 px-2 text-right font-mono-angka text-slate-700">Rp {{ number_format($faktur->total_bruto, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-2 text-right font-mono-angka font-bold text-slate-900">Rp {{ number_format($faktur->total_bruto, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Ringkasan Finansial Total Pembayaran -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t-2 border-slate-200">
            <!-- Informasi Rekening Pembayaran Resmi -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs">
                <span class="font-bold text-slate-800 block mb-1.5 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Rekening Pembayaran Resmi PT PBJ:
                </span>
                <div class="space-y-1 text-[11px] text-slate-600">
                    <p>• <span class="font-semibold text-slate-800">Bank Mandiri:</span> <span class="font-mono-angka text-slate-900 font-bold">1300-0987-6543-2</span> (a/n PT Putra Balkom Jaya)</p>
                    <p>• <span class="font-semibold text-slate-800">Bank BRI:</span> <span class="font-mono-angka text-slate-900 font-bold">0012-01-000999-30-5</span> (a/n PT Putra Balkom Jaya)</p>
                    <p>• <span class="font-semibold text-slate-800">Bank BCA:</span> <span class="font-mono-angka text-slate-900 font-bold">8800-1234-5678</span> (a/n PT Putra Balkom Jaya)</p>
                </div>
                <p class="text-[10px] text-slate-500 italic mt-2">
                    * Pembayaran sah apabila dana telah masuk ke rekening resmi perusahaan atau tercatat pada kuitansi resmi bertanda tangan kasir.
                </p>
            </div>

            <!-- Kalkulasi Nominal & Status Piutang -->
            <div class="space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Total Penjualan (Bruto):</span>
                    <span class="font-mono-angka font-semibold">Rp {{ number_format($faktur->total_bruto, 0, ',', '.') }}</span>
                </div>
                @if($faktur->diskon > 0)
                <div class="flex justify-between text-emerald-600">
                    <span>Potongan Diskon / Khusus:</span>
                    <span class="font-mono-angka font-semibold">- Rp {{ number_format($faktur->diskon, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-t border-b border-slate-300 font-bold text-slate-900 text-sm">
                    <span>Total Tagihan Bersih (Netto):</span>
                    <span class="font-mono-angka text-indigo-700 text-base">Rp {{ number_format($faktur->total_netto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Jumlah Telah Dibayar:</span>
                    <span class="font-mono-angka font-semibold text-emerald-600">Rp {{ number_format($faktur->jumlah_dibayar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-800">
                    <span>Sisa Tagihan / Piutang:</span>
                    <span class="font-mono-angka {{ $faktur->sisa_piutang > 0 ? 'text-rose-600' : 'text-slate-700' }}">
                        Rp {{ number_format($faktur->sisa_piutang, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Kolom Tanda Tangan & Pengesahan Dokumen -->
        <div class="grid grid-cols-3 gap-6 pt-12 mt-6 border-t border-slate-200 text-center text-xs">
            <div>
                <p class="text-[11px] text-slate-500 mb-16">Penerima Barang / Toko,</p>
                <div class="border-b border-slate-400 w-36 mx-auto"></div>
                <p class="text-[10px] text-slate-400 mt-1">Cap & Tanda Tangan</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 mb-16">Pengemudi / Ekspedisi,</p>
                <div class="border-b border-slate-400 w-36 mx-auto"></div>
                <p class="text-[10px] text-slate-400 mt-1">Nama Jelas Supir</p>
            </div>
            <div>
                <p class="text-[11px] text-slate-500 mb-16">Hormat Kami,<br><span class="font-bold text-slate-800">PT PUTRA BALKOM JAYA</span></p>
                <div class="border-b border-slate-400 w-36 mx-auto"></div>
                <p class="text-[10px] text-slate-400 mt-1">Bagian Keuangan / Kasir</p>
            </div>
        </div>

        <!-- Footer Barcode & Legalitas -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
            <span>Dicetak secara otomatis oleh Sistem ERP PBJ pada: {{ now()->translatedFormat('d F Y, H:i:s') }} WIB</span>
            <span class="font-mono-angka">ID Dokumen: {{ md5($faktur->nomor_faktur . $faktur->tanggal_penjualan) }}</span>
        </div>

    </div>

</body>
</html>
