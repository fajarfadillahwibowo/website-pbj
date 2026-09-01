<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Informasi Akuntansi & Distribusi Semen')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-100 flex overflow-hidden">

    {{-- Navigation Sidebar --}}
    @include('layouts.sidebar')

    {{-- Main Content Container --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- Header Topbar --}}
        @include('layouts.header')

        {{-- Dynamic Page Body --}}
        <main class="flex-1 overflow-y-auto p-6 bg-slate-900/50">
            @yield('content')
        </main>
    </div>

</body>
</html>
