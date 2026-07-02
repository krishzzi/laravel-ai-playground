<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#020617">

    <title>@yield('title', 'AI Relm — Elite AI Product Systems')</title>
    <meta name="description" content="@yield('meta_description', 'AI Relm builds elite-grade AI products, agents, and intelligent interfaces for modern businesses.')">

    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,400,500,700,900&f[]=clash-display@400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Livewire styles --}}
    @livewireStyles
    @stack('styles')
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased font-sans">
<div id="app" class="min-h-full">
    {{ $slot ?? '' }}
    @yield('content')
</div>

@stack('scripts')
@livewireScripts
</body>
</html>
