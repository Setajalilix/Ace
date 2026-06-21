<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#FAF7F2] text-[#3D3229] flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center fade-in">
        <div class="w-16 h-16 rounded-2xl bg-[#C47D5A]/10 flex items-center justify-center mx-auto mb-6">
            <span class="text-3xl">@yield('emoji', '🌿')</span>
        </div>
        <p class="text-sm font-semibold text-[#C47D5A] tracking-wider uppercase mb-2">@yield('code')</p>
        <h1 class="text-2xl font-semibold mb-2">@yield('title')</h1>
        <p class="text-[#A8958B] text-sm leading-relaxed mb-8">@yield('message')</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="btn-primary">Back to Today</a>
            @hasSection('secondary')
                @yield('secondary')
            @else
                <button type="button" onclick="history.back()" class="btn-secondary">Go back</button>
            @endif
        </div>
    </div>
</body>
</html>
