<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}"class="h-full">

<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>{{ config('app.name','EconoLlantas') }} - Player</title>

 <!-- Google Fonts & Icons -->
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
 rel="stylesheet">
 <link rel="stylesheet"
 href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>

 @vite(['resources/css/app.css','resources/js/app.js'])

 <style>
 [x-cloak] {
 display: none !important;
 }

 :root {
 --primary-color:
 {{ \App\Modules\Settings\Models\Setting::get('theme_color','#363d82') }}
 ;
 }

 .bg-primary {
 background-color: var(--primary-color) !important;
 }

 .text-primary {
 color: var(--primary-color) !important;
 }

 .border-primary {
 border-color: var(--primary-color) !important;
 }
 </style>

 <script>
 function applyTheme() {
 if (localStorage.getItem('darkMode') ==='true'|| (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
 document.documentElement.classList.add('dark');
 } else {
 document.documentElement.classList.remove('dark');
 }
 }
 applyTheme();
 document.addEventListener('livewire:navigated', applyTheme);
 </script>
</head>

<body class="h-full overflow-hidden bg-slate-50 font-sans antialiased"
 x-data="{ darkMode: localStorage.getItem('darkMode') ==='true'}" x-init="$watch('darkMode', val => { 
 localStorage.setItem('darkMode', val); 
 if(val) document.documentElement.classList.add('dark'); 
 else document.documentElement.classList.remove('dark');
 })">
 {{ $slot }}
</body>

</html>