<!DOCTYPE html>
<html lang="en" x-data="{ mobileMenuOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — IntraEats HR</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-[#FAFAF8] text-[#1C1C1E] min-h-screen antialiased flex flex-col selection:bg-[#FF6B1A]/20 selection:text-[#FF6B1A]">

    <!-- Desktop Sidebar (Fixed) -->
    <aside class="hidden lg:flex fixed left-0 top-0 bottom-0 w-64 bg-[#1C1C1E] text-white flex-col justify-between z-50 border-r border-[#2E2E33] shadow-lg">
        <div class="flex flex-col">
            <!-- Brand Logo & Header -->
            <div class="h-16 px-6 flex items-center gap-3 border-b border-[#2E2E33]">
                <div class="w-9 h-9 rounded-xl bg-[#FF6B1A] flex items-center justify-center text-white font-extrabold text-base shadow-sm">
                    <i data-lucide="utensils" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-extrabold text-base tracking-tight leading-none text-white">IntraEats</span>
                    <span class="text-[11px] font-bold text-[#FF6B1A] uppercase tracking-wider mt-1">HR &amp; Attendance</span>
                </div>
            </div>

            <!-- Navigation Section Label -->
            <div class="px-6 pt-5 pb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#A1A1AA]">Workforce Core</span>
            </div>

            <!-- Navigation Links -->
            <nav class="flex flex-col gap-1 px-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all relative {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.attendance.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    <span>Attendance Ledger</span>
                </a>

                <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.employees.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Employees</span>
                </a>

                <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.projects.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                    <span>Projects &amp; Tasks</span>
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span>Reports &amp; Export</span>
                </a>

                @if(auth()->user()->hasRole('Super Admin'))
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.users.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Admin Users</span>
                </a>
                @endif

                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    <span>Settings &amp; Rules</span>
                </a>

                <a href="{{ route('admin.audit.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('admin.audit.*') ? 'bg-[#FF6B1A] text-white font-bold shadow-md shadow-[#FF6B1A]/20' : 'text-[#D4D4D8] hover:bg-[#2E2E33] hover:text-white' }}">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span>Audit Trail</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-4 flex flex-col gap-3">
            <div class="bg-[#24242A] rounded-xl p-3 border border-[#2E2E33] flex flex-col gap-1.5">
                <div class="flex items-center gap-1.5 text-xs text-[#16A34A] font-semibold">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-pulse"></span>
                    <span>Terminal Kiosk Sync</span>
                </div>
                <p class="text-[11px] text-[#A1A1AA]">Employee attendance kiosks active.</p>
                <a href="{{ route('employee.portal') }}" target="_blank" class="mt-1 flex items-center justify-center gap-1.5 py-1.5 px-3 bg-[#1C1C1E] hover:bg-[#2E2E33] text-white text-xs font-semibold rounded-lg transition-all border border-[#3E3E46]">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                    <span>Open Kiosk Portal</span>
                </a>
            </div>

            <!-- Current Logged in User Bar -->
            <div class="flex items-center justify-between pt-2 border-t border-[#2E2E33] text-xs">
                <div class="flex items-center gap-2">
                    <img src="{{ auth()->user()->avatar_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-[#FF6B1A]" alt="Admin">
                    <div class="flex flex-col truncate max-w-[120px]">
                        <span class="font-bold text-white truncate">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-[#A1A1AA]">{{ auth()->user()->role_name }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Logout" class="p-1.5 rounded-lg text-[#A1A1AA] hover:text-red-400 hover:bg-[#2E2E33] transition-all">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Top Sticky Header -->
        <header class="sticky top-0 bg-white/95 backdrop-blur-md border-b border-[#E2E8F0] h-16 z-40 px-3 sm:px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <button @click="mobileMenuOpen = !mobileMenuOpen; $nextTick(() => window.createIcons({ icons: window.lucideIcons }))" class="lg:hidden p-2 rounded-lg text-[#1C1C1E] hover:bg-[#F1F5F9] shrink-0" aria-label="Toggle Navigation">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <div class="flex flex-col min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-sm sm:text-base md:text-lg text-[#1C1C1E] tracking-tight truncate">IntraEats &amp; Talisha HR</span>
                        <span class="hidden sm:inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF3EB] text-[#FF6B1A] uppercase border border-[#FFD4BD] shrink-0">Enterprise</span>
                    </div>
                    <span class="hidden md:inline-block text-xs text-[#64748B]">Automated Workforce Telemetry &bull; Asia/Kolkata</span>
                </div>
            </div>

            <!-- Top Header Right Actions -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Live Clock Pill -->
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-xs font-mono font-medium text-[#1E293B]">
                    <span class="w-2 h-2 rounded-full bg-[#16A34A] animate-ping"></span>
                    <span id="headerLiveClock">{{ now()->format('h:i:s A') }}</span>
                    <span class="text-[#94A3B8]">IST</span>
                </div>

                <!-- Open Kiosk Quick Link -->
                <a href="{{ route('employee.portal') }}" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#1E293B] transition-all">
                    <i data-lucide="tablet" class="w-3.5 h-3.5 text-[#FF6B1A]"></i>
                    <span>Kiosk Mode</span>
                </a>

                <!-- User Dropdown & Logout -->
                <div class="flex items-center gap-2 pl-2 border-l border-[#E2E8F0]" x-data="{ userMenu: false }">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-2 p-1 rounded-lg hover:bg-[#F8FAFC]">
                        <img src="{{ auth()->user()->avatar_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80' }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-[#FF6B1A]/20" alt="Avatar">
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="text-xs font-bold text-[#1C1C1E] leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-[#64748B] leading-tight">{{ auth()->user()->role_name }}</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-[#94A3B8]"></i>
                    </button>

                    <div x-show="userMenu" @click.away="userMenu = false" x-cloak class="absolute right-4 top-14 w-48 bg-white rounded-xl shadow-xl border border-[#E2E8F0] py-1 z-50">
                        <div class="px-4 py-2 border-b border-[#F1F5F9]">
                            <p class="text-xs font-bold text-[#1C1C1E] truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-[#64748B] truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#1E293B] hover:bg-[#F8FAFC]">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-[#64748B]"></i>
                            <span>My Profile</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-[#1E293B] hover:bg-[#F8FAFC]">
                            <i data-lucide="key" class="w-3.5 h-3.5 text-[#64748B]"></i>
                            <span>Change Password</span>
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-red-600 hover:bg-red-50 text-left">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 sm:px-8 pt-4">
            @if(session('success'))
            <div class="p-3.5 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] flex items-center justify-between text-sm shadow-sm" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="check-circle" class="w-5 h-5 text-[#10B981] shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-[#065F46] hover:text-black">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="p-3.5 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#991B1B] flex items-center justify-between text-sm shadow-sm mt-2" x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-[#EF4444] shrink-0"></i>
                    <div>
                        {{ session('error') ?? $errors->first() }}
                    </div>
                </div>
                <button @click="show = false" class="text-[#991B1B] hover:text-black">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            @endif
        </div>

        <!-- Page Content -->
        <main class="flex-1 px-4 sm:px-8 py-6">
            @yield('content')
        </main>

        <!-- Admin Footer -->
        <footer class="mt-auto px-4 sm:px-8 py-4 border-t border-[#E2E8F0] bg-white text-xs text-[#64748B] flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                &copy; {{ date('Y') }} <strong>IntraEats &amp; Talisha Software</strong> &bull; HR &amp; Attendance Management System
            </div>
            <div class="flex items-center gap-3">
                <span>Production Mode (Hostinger-Ready)</span>
                <span>&bull;</span>
                <span class="font-mono">v3.0.0 Laravel 11</span>
            </div>
        </footer>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-50 lg:hidden flex">
        <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>
        <div class="relative w-72 max-w-[85vw] bg-[#1C1C1E] text-white flex flex-col justify-between p-5 z-10 shadow-2xl overflow-y-auto">
            <div class="flex flex-col">
                <div class="flex items-center justify-between pb-4 border-b border-[#2E2E33]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#FF6B1A] flex items-center justify-center text-white font-extrabold text-sm">
                            <i data-lucide="utensils" class="w-4 h-4"></i>
                        </div>
                        <span class="font-extrabold text-white text-base">IntraEats HR</span>
                    </div>
                    <button @click="mobileMenuOpen = false" class="text-[#A1A1AA] hover:text-white p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <nav class="flex flex-col gap-1.5 mt-5">
                    <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.attendance.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.attendance.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="user-check" class="w-4 h-4"></i> Attendance
                    </a>
                    <a href="{{ route('admin.employees.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.employees.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="users" class="w-4 h-4"></i> Employees
                    </a>
                    <a href="{{ route('admin.projects.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.projects.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="briefcase" class="w-4 h-4"></i> Projects
                    </a>
                    <a href="{{ route('admin.reports.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.reports.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Reports
                    </a>
                    @if(auth()->user()->hasRole('Super Admin'))
                    <a href="{{ route('admin.users.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.users.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="shield-check" class="w-4 h-4"></i> Admin Users
                    </a>
                    @endif
                    <a href="{{ route('admin.settings.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.settings.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="sliders" class="w-4 h-4"></i> Settings
                    </a>
                    <a href="{{ route('admin.audit.index') }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm {{ request()->routeIs('admin.audit.*') ? 'bg-[#FF6B1A] text-white font-bold' : 'text-[#D4D4D8] hover:bg-[#2E2E33]' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i> Audit Logs
                    </a>
                </nav>

                <div class="mt-4 pt-3 border-t border-[#2E2E33]">
                    <a href="{{ route('employee.portal') }}" target="_blank" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-[#A1A1AA] hover:text-white hover:bg-[#2E2E33]">
                        <i data-lucide="external-link" class="w-4 h-4 text-[#FF6B1A]"></i>
                        <span>Open Attendance Kiosk</span>
                    </a>
                </div>
            </div>

            <div class="pt-4 border-t border-[#2E2E33] mt-4">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-red-600/20 text-red-400 hover:bg-red-600 hover:text-white text-xs font-bold transition-all">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Live clock ticker script -->
    <script>
        setInterval(() => {
            const clockEl = document.getElementById('headerLiveClock');
            if (clockEl) {
                const now = new Date();
                clockEl.innerText = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            }
        }, 1000);
    </script>

    @stack('scripts')
</body>
</html>
