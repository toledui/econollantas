<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $primaryColor = \App\Modules\Settings\Models\Setting::get('theme_color', '#363d82');
        $footerText = \App\Modules\Settings\Models\Setting::get('footer_text', '©' . date('Y') . 'EconoLlantas. Todos los derechos reservados.');
        $siteLogo = \App\Modules\Settings\Models\Setting::get('site_logo', 'econollantaslogo.png');
    @endphp

    <style id="dynamic-theme">
        :root {
            --primary-color:
                {{ $primaryColor }}
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

        .focus\:ring-primary:focus {
            --tw-ring-color: var(--primary-color) !important;
        }

        .focus\:border-primary:focus {
            border-color: var(--primary-color) !important;
        }
    </style>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-active {
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid white;
        }

        .bg-watermark {
            background-image: url('{{ asset('storage/' . $siteLogo) }}');
            background-repeat: repeat;
            background-size: 150px;
            opacity: 0.04;
            filter: grayscale(1);
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 3s infinite ease-in-out;
        }

        @keyframes bounce-subtle {

            0%,
            50%,
            100% {
                transform: translateY(0);
            }

            25% {
                transform: translateY(-3px);
            }
        }

        .pulse-red {
            animation: pulse-red-anim 1.5s infinite ease-in-out;
        }

        @keyframes pulse-red-anim {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
            }

            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Material Symbols adjustment */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-900 h-full overflow-hidden" x-data="{ 
        sidebarOpen: true,
        mobileMenuOpen: false,
        primaryColor: '{{ $primaryColor }}'
    }"
    @theme-updated.window="primaryColor = $event.detail.color; document.getElementById('dynamic-theme').innerHTML = `:root { --primary-color: ${$event.detail.color}; } .bg-primary { background-color: var(--primary-color) !important; } .text-primary { color: var(--primary-color) !important; } .border-primary { border-color: var(--primary-color) !important; } .focus\\:ring-primary:focus { --tw-ring-color: var(--primary-color) !important; } .focus\\:border-primary:focus { border-color: var(--primary-color) !important; }`"
    x-init="
        document.addEventListener('livewire:navigated', () => {
            mobileMenuOpen = false;
        });
    ">
    <div class="bg-watermark"></div>

    <div class="flex h-screen overflow-hidden">

        {{-- ═══ Mobile Menu Overlay (backdrop) ═══ --}}
        <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
            class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-30"></div>

        {{-- ═══ Sidebar ═══ --}}
        <aside
            class="bg-primary text-white flex flex-col flex-shrink-0 z-40 transition-all duration-300 fixed inset-y-0 left-0 lg:relative lg:translate-x-0"
            :class="{'w-64' : sidebarOpen, 'w-20' : !sidebarOpen, '-translate-x-full' : !mobileMenuOpen, 'translate-x-0' : mobileMenuOpen}"
            x-bind:class="window.innerWidth >= 1024 ? (sidebarOpen ? 'w-64 translate-x-0' : 'w-20 translate-x-0') : (mobileMenuOpen ? 'w-72 translate-x-0' : 'w-72 -translate-x-full')">

            <div class="px-6 py-8">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo EconoLlantas"
                        class="h-12 w-auto transition-all duration-300"
                        :class="sidebarOpen ? 'h-12 w-auto' : 'h-8 w-8 object-contain'">
                </div>
            </div>

            <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto no-scrollbar">
                <x-nav-link-custom href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                    icon="dashboard" label="Dashboard" />
                <x-nav-link-custom href="{{ route('courses.user-index') }}"
                    :active="request()->routeIs('courses.user-index*')" icon="auto_stories" label="Mis Cursos" />
                @if(Auth::user()->hasPermission('courses.view'))
                    <x-nav-link-custom href="{{ route('courses') }}" :active="request()->routeIs('courses*') && !request()->routeIs('courses.user-index*')" icon="school" label="Cursos" />
                @endif
                @if(Auth::user()->hasPermission('library.view'))
                    <x-nav-link-custom href="{{ route('library') }}" :active="request()->routeIs('library')"
                        icon="library_books" label="Biblioteca" />
                @endif

                @if(Auth::user()->hasPermission('users.view') || Auth::user()->hasPermission('departments.view') || Auth::user()->hasPermission('branches.view') || Auth::user()->hasPermission('announcements.view') || Auth::user()->hasPermission('reports.view') || Auth::user()->hasPermission('settings.view'))
                        <div class="pt-4 pb-2 px-4 whitespace-nowrap overflow-hidden" x-show="sidebarOpen || mobileMenuOpen"
                        x-cloak>
                        <div class="text-[10px] uppercase tracking-widest text-white/50 font-bold">Administración</div>
                    </div>
                @endif

                @if(Auth::user()->hasPermission('users.view'))
                    <x-nav-link-custom href="{{ route('users') }}" :active="request()->routeIs('users')" icon="group"
                        label="Usuarios" />
                @endif
                @if(Auth::user()->hasPermission('departments.view'))
                    <x-nav-link-custom href="{{ route('departments') }}" :active="request()->routeIs('departments')"
                        icon="corporate_fare" label="Departamentos" />
                @endif
                @if(Auth::user()->hasPermission('branches.view'))
                    <x-nav-link-custom href="{{ route('branches') }}" :active="request()->routeIs('branches')"
                        icon="location_on" label="Sucursales" />
                @endif
                @if(Auth::user()->hasPermission('announcements.view'))
                    <x-nav-link-custom href="{{ route('announcements') }}" :active="request()->routeIs('announcements')"
                        icon="campaign" label="Avisos" />
                @endif
                @if(Auth::user()->hasPermission('reports.view'))
                    <x-nav-link-custom href="{{ route('reports') }}" :active="request()->routeIs('reports*')"
                        icon="analytics" label="Reportes" />
                @endif

                @if(Auth::user()->hasPermission('settings.view'))
                    <div x-data="{ expanded: {{ request()->routeIs('settings*') ? 'true' : 'false' }} }">
                        <button @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 group relative"
                            :class="expanded || '{{ request()->routeIs('settings*') }}' ? 'bg-white/10 text-white shadow-lg' : 'text-white/70 hover:bg-white/5 hover:text-white'">
                            <div class="flex items-center">
                                <span class="material-symbols-outlined mr-3 transition-transform group-hover:scale-110"
                                    :class="expanded || '{{ request()->routeIs('settings*') }}' ? 'text-white fill-1' : 'text-white/70'">settings</span>
                                <span class="text-sm font-bold tracking-wide"
                                    x-show="sidebarOpen || mobileMenuOpen">Configuración</span>
                            </div>
                            <span class="material-symbols-outlined text-sm transition-transform duration-200"
                                :class="expanded ? 'rotate-180' : ''"
                                x-show="sidebarOpen || mobileMenuOpen">expand_more</span>
                        </button>

                        <div x-show="expanded && (sidebarOpen || mobileMenuOpen)" x-cloak class="mt-1 space-y-1 px-4">
                            <a href="{{ route('settings') }}"
                                class="flex items-center px-4 py-2 rounded-lg text-xs font-medium transition-colors border-l-2 ml-4"
                                :class="'{{ request()->routeIs('settings') }}' ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/50 hover:text-white hover:bg-white/5'">
                                General
                            </a>
                            <a href="{{ route('settings.mail') }}"
                                class="flex items-center px-4 py-2 rounded-lg text-xs font-medium transition-colors border-l-2 ml-4"
                                :class="'{{ request()->routeIs('settings.mail') }}' ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/50 hover:text-white hover:bg-white/5'">
                                Correo (SMTP)
                            </a>
                            <a href="{{ route('settings.roles') }}"
                                class="flex items-center px-4 py-2 rounded-lg text-xs font-medium transition-colors border-l-2 ml-4"
                                :class="'{{ request()->routeIs('settings.roles') }}' ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/50 hover:text-white hover:bg-white/5'">
                                Roles y Permisos
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            {{-- Desktop: sidebar toggle --}}
            <div class="p-4 border-t border-white/10 hidden lg:block">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-full flex items-center justify-center p-2 hover:bg-white/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined transition-transform duration-300"
                        :class="!sidebarOpen ? 'rotate-180' : ''">keyboard_double_arrow_left</span>
                </button>
            </div>
            {{-- Mobile: close button --}}
            <div class="p-4 border-t border-white/10 lg:hidden">
                <button @click="mobileMenuOpen = false"
                    class="w-full flex items-center justify-center gap-2 p-2 hover:bg-white/10 rounded-lg transition-colors text-sm font-bold">
                    <span class="material-symbols-outlined">close</span> Cerrar menú
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="h-16 bg-primary text-white flex items-center justify-between px-4 lg:px-8 shadow-md z-10 transition-colors duration-300">

                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 hover:bg-white/10 rounded-xl transition-all">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <img src="{{ asset('storage/' . $siteLogo) }}" alt="Logo" class="h-8 w-auto lg:hidden">
                </div>

                <div class="hidden sm:flex items-center flex-1 max-w-xl">
                    <div class="relative w-full group">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-white/70">search</span>
                        <input type="text"
                            class="w-full bg-white/10 border-none rounded-lg py-2 pl-10 pr-4 text-sm text-white placeholder:text-white/60 focus:ring-2 focus:ring-white/30 focus:bg-white/20 transition-all outline-none"
                            placeholder="Buscar cursos, manuales o usuarios...">
                    </div>
                </div>

                <div class="flex items-center gap-4 lg:gap-6">
                    <livewire:notification-dropdown />

                    <div class="h-8 w-px bg-white/20 hidden sm:block"></div>

                    <div class="flex items-center gap-3 relative" x-data="{ userMenu: false }">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-white/70 mt-1">
                                {{ Auth::user()->primaryBranch?->name ?? 'Sucursal Norte' }}
                            </p>
                        </div>
                        <button @click="userMenu = !userMenu"
                            class="size-10 rounded-full bg-white/20 border-2 border-white/10 overflow-hidden hover:border-white transition-colors">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="size-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=363d82"
                                    alt="Avatar">
                            @endif
                        </button>

                        <div x-show="userMenu" @click.away="userMenu = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100" x-cloak
                            class="absolute right-0 top-12 w-48 bg-white rounded-lg shadow-xl border border-slate-200 py-2 z-50 text-slate-700">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Mi
                                Perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left block px-4 py-2 text-sm hover:bg-slate-50 text-red-600 font-bold">Cerrar
                                    Sesión</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 space-y-4 lg:space-y-8 no-scrollbar flex flex-col">
                <div class="flex-1">
                    {{ $slot }}
                </div>

                <footer class="mt-auto pt-8 pb-4 text-center border-t border-slate-200">
                    <p class="text-sm text-slate-500">
                        {{ $footerText }}
                    </p>
                </footer>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#000000',
                customClass: {
                    popup: 'rounded-2xl border border-slate-200 shadow-2xl',
                    timerProgressBar: 'bg-primary'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)

                    const progressBar = toast.querySelector('.swal2-timer-progress-bar');
                    if (progressBar) progressBar.style.backgroundColor = '#363d82';
                }
            });

            window.addEventListener('toast', event => {
                const data = event.detail[0];

                Toast.fire({
                    icon: data.type,
                    title: data.message,
                    background: '#ffffff',
                    color: '#000000',
                });
            });
        });
    </script>
</body>

</html>