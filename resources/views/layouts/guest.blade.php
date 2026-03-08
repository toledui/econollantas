<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EconoLlantas') }}</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('storage/favicon/site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('storage/favicon/favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-900 bg-slate-50">
    @php
        $siteLogo = \App\Modules\Settings\Models\Setting::get('site_logo', 'econollantaslogo.png');
    @endphp

    <div class="min-h-screen flex items-center justify-center relative overflow-hidden p-4">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full sm:max-w-md relative z-10">
            <div class="flex justify-center mb-8">
                <a href="/" wire:navigate>
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="EconoLlantas"
                        class="h-20 w-auto drop-shadow-xl hover:scale-105 transition-transform duration-300">
                </a>
            </div>

            <div class="bg-white shadow-2xl shadow-slate-200/50 rounded-3xl px-8 py-10 border border-slate-100">
                {{ $slot }}
            </div>

            <p class="text-center mt-8 text-xs font-medium text-slate-500">
                &copy; {{ date('Y') }} EconoLlantas. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>

</html>