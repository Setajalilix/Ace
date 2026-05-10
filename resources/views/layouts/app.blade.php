<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">

</head>
<body class="bg-zinc-950 text-zinc-100 min-h-screen antialiased">

<div class="fixed inset-0 overflow-hidden pointer-events-none">

    <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-indigo-500/10 blur-3xl rounded-full">
    </div>

    <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-fuchsia-500/10 blur-3xl rounded-full">
    </div>

</div>

<div class="relative z-10 flex min-h-screen">

    <aside class="hidden lg:flex w-72 border-l border-white/5 bg-white/5 backdrop-blur-xl flex-col">

        <div class="p-8 border-b border-white/5">

            <img src="{{asset('logo.png')}}" alt="logo">

            <p class="text-zinc-400 mt-2 text-sm leading-7">
                عمر گران می‌گذرد خواهی نخواهی
            </p>

        </div>

        <nav class="flex-1 p-5 space-y-2">
            <a href="{{ route('dashboard') }}"
               class="sidebar-link active">
                داشبورد
            </a>

            <a href="{{ route('habits.index') }}"
               class="sidebar-link">
                عادت ها
            </a>

            <a href="#"
               class="sidebar-link">
                تقویم
            </a>

            <a href="#"
               class="sidebar-link">
                گزارشات
            </a>

        </nav>

        <div class="p-6 border-t border-white/5 text-sm text-zinc-500">
            طراحی شده توسط دایاد فقط در یک شب
        </div>

    </aside>

    <main class="flex-1 overflow-y-auto">

        <div class="max-w-7xl mx-auto px-6 py-8 lg:px-10">
            {{ $slot ?? '' }}

            @yield('content')
        </div>

    </main>

</div>

</body>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</html>
