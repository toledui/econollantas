<x-app-layout>
 <x-slot name="header">
 <h2 class="font-semibold text-xl text-gray-800 leading-tight">
 {{ __('Dashboard') }}
 </h2>
 </x-slot>

 <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
 <div
 class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-200 mb-8">
 <div class="p-8 text-slate-900">
 <h3 class="text-2xl font-black uppercase tracking-tight mb-2">Bienvenido de nuevo,
 {{ Auth::user()->name }}!
 </h3>
 <p class="text-slate-500">Has iniciado sesión correctamente. Selecciona una opción
 del menú lateral para comenzar.</p>
 </div>
 </div>

 <!-- Announcements Banner (at the bottom) -->
 <livewire:announcements.banner />
 </div>
</x-app-layout>