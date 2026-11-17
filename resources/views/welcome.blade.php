<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Report - Sistem Layanan Pengaduan Warga Modern</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        [x-cloak] { display: none !important; }

        .glass-header {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .hero-glow {
            background: radial-gradient(circle at 50% -10%, rgba(16, 185, 129, 0.16) 0%, rgba(248, 250, 252, 0) 70%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 0.4; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }
        .animate-pulse-ring {
            animation: pulse-ring 3s infinite ease-in-out;
        }
        .form-label {
            display: block;
            margin-bottom: 0.375rem;
            color: #334155;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .form-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }
        @media print {
            header, footer, .toast, body > div.fixed { display: none !important; }
            section[x-show]:not([x-show*="detail-pengaduan"]) { display: none !important; }
            section[x-show*="detail-pengaduan"] {
                display: block !important;
                max-width: none !important;
                padding: 0 !important;
            }
            section[x-show*="detail-pengaduan"] button,
            section[x-show*="detail-pengaduan"] form { display: none !important; }
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        forest: {
                            700: '#164232',
                            750: '#12392b',
                            800: '#0f2f23',
                            850: '#0d281e',
                            900: '#0b2118',
                            950: '#071711',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col relative selection:bg-brand-500 selection:text-white" x-data="reportApp()">

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="fixed top-20 right-5 z-50 flex flex-col space-y-3 max-w-sm pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto p-4 rounded-2xl shadow-2xl border flex items-center space-x-3 transition-all duration-300 transform translate-y-0"
                 :class="{
                     'bg-emerald-900 text-white border-emerald-700': toast.type === 'success',
                     'bg-amber-900 text-white border-amber-700': toast.type === 'warning',
                     'bg-emerald-950 text-white border-emerald-800 shadow-xl': toast.type === 'info',
                     'bg-red-900 text-white border-red-700': toast.type === 'error'
                 }">
                <i :data-lucide="toast.icon" class="w-5 h-5 shrink-0"></i>
                <div class="flex-grow text-xs font-semibold" x-text="toast.message"></div>
                <button @click="removeToast(toast.id)" class="text-white/60 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        </template>
    </div>

    <!-- TOP NAVIGATION BAR -->
    <header x-show="!(isLoggedIn && (role === 'admin' || role === 'warga')) || currentPage === 'landing'" class="glass-header border-b border-slate-200/80 sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">

            <!-- Logo & Brand Name -->
            <div class="flex items-center space-x-3 cursor-pointer group" @click="currentPage = 'landing'">
                <div class="relative">
                    <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-teal-400 rounded-2xl blur opacity-30 group-hover:opacity-75 transition duration-300"></div>
                    <div class="relative bg-gradient-to-tr from-emerald-700 via-emerald-600 to-teal-500 text-white p-3 rounded-2xl shadow-lg shadow-emerald-700/20 group-hover:scale-105 transition-transform">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xl font-black tracking-tight text-slate-900">E-REPORT</span>
                    </div>
                    <span class="block text-[10px] font-bold text-slate-400 tracking-wider uppercase">Layanan Pengaduan & Aspirasi Publik</span>
                </div>
            </div>

            <!-- Role Switcher & Navigation Links -->
            <div class="flex items-center space-x-3 sm:space-x-5">

                <!-- Role Switcher Badge (Simulator Feature - Modul 1 & 10) -->
                <div class="hidden lg:flex items-center bg-slate-100/90 p-1.5 rounded-2xl border border-slate-200/80 shadow-inner">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest px-2">Modul Akses:</span>
                    <button @click="setRole('warga')" :class="role === 'warga' ? 'bg-white shadow-md text-emerald-800 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-medium'" class="px-3 py-1.5 rounded-xl text-xs transition duration-200">
                        <i data-lucide="users" class="w-3.5 h-3.5 inline mr-1"></i> Warga
                    </button>
                    <button @click="setRole('admin')" :class="role === 'admin' ? 'bg-[#0b2118] shadow-md text-emerald-300 font-extrabold' : 'text-slate-500 hover:text-slate-800 font-medium'" class="px-3 py-1.5 rounded-xl text-xs transition duration-200">
                        <i data-lucide="shield-alert" class="w-3.5 h-3.5 inline mr-1"></i> Petugas / Admin
                    </button>
                </div>

                <!-- Navigation menu based on page/role -->
                <template x-if="isLoggedIn">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <button @click="currentPage = role === 'admin' ? 'admin-dashboard' : 'warga-dashboard'"
                                :class="currentPage.includes('dashboard') ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200/60' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3.5 py-2 rounded-xl text-xs font-semibold transition flex items-center space-x-1.5">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Dashboard</span>
                        </button>

                        <template x-if="role === 'warga'">
                            <button @click="currentPage = 'warga-buat'" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 rounded-xl text-xs font-extrabold flex items-center space-x-1.5 shadow-md shadow-emerald-600/20 hover:shadow-emerald-600/30 transition">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                <span>Buat Pengaduan</span>
                            </button>
                        </template>

                        <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- User Profile Info Button -->
                        <button @click="navigateToProfile()" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-slate-100 transition">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-700 to-[#0b2118] text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                <span x-text="currentUser.initial"></span>
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="block text-xs font-bold text-slate-800" x-text="currentUser.nama"></span>
                                <span class="block text-[10px] text-slate-400 capitalize" x-text="role"></span>
                            </div>
                        </button>

                        <button @click="logout()" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Keluar">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </div>
                </template>

                <template x-if="!isLoggedIn">
                    <div class="flex items-center space-x-2">
                        <button @click="currentPage = 'login'" class="px-4 py-2.5 text-xs font-bold text-slate-700 hover:text-emerald-700 rounded-xl transition">Masuk</button>
                        <button @click="currentPage = 'register'" class="bg-[#0b2118] hover:bg-[#133527] text-white px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:shadow-lg transition">Daftar Akun</button>
                    </div>
                </template>
            </div>
        </div>
    </header>

    <!-- PERMANENT ADMIN SIDEBAR -->
    <aside x-show="isLoggedIn && role === 'admin' && currentPage !== 'landing'"
           class="fixed inset-y-0 left-0 z-40 flex w-20 flex-col border-r border-[#153427] bg-[#0b2118] shadow-2xl shadow-emerald-950/40 lg:w-72">
        <div class="flex h-24 items-center justify-center border-b border-white/5 lg:px-7 lg:justify-start">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-lg shadow-emerald-900/40">
                    <i data-lucide="shield-check" class="h-5 w-5"></i>
                </div>
                <div class="hidden lg:block">
                    <p class="text-lg font-black tracking-tight text-white">E-REPORT</p>
                    <p class="text-[9px] font-bold uppercase tracking-[0.24em] text-emerald-400/70">Control Center</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Quick Search (Reference Style) -->
        <div class="hidden lg:block px-4 pt-5 pb-2">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3.5 top-3 h-3.5 w-3.5 text-emerald-300/40"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari laporan..." class="w-full rounded-xl bg-white/5 border border-white/10 py-2 pl-9 pr-3 text-xs text-white placeholder:text-emerald-200/40 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/50 transition">
            </div>
        </div>

        <nav class="flex-1 px-2 py-4 lg:px-4">
            <p class="hidden px-4 pb-3 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400/60 lg:block">Navigasi Utama</p>
            <div class="space-y-1.5">
                <button @click="currentPage = 'admin-dashboard'"
                        :class="currentPage === 'admin-dashboard' ? 'bg-[#163a2c] text-emerald-300 border border-emerald-500/20 shadow-md shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Dashboard">
                    <i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0"></i>
                    <span class="hidden lg:inline">Dashboard</span>
                </button>
                <button @click="currentPage = 'admin-pengaduan'"
                        :class="currentPage === 'admin-pengaduan' ? 'bg-[#163a2c] text-emerald-300 border border-emerald-500/20 shadow-md shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Kelola Pengaduan">
                    <i data-lucide="clipboard-list" class="h-5 w-5 shrink-0"></i>
                    <span class="hidden lg:inline">Kelola Pengaduan</span>
                </button>
                <button @click="currentPage = 'admin-petugas'"
                        :class="currentPage === 'admin-petugas' ? 'bg-[#163a2c] text-emerald-300 border border-emerald-500/20 shadow-md shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Data Petugas">
                    <i data-lucide="users" class="h-5 w-5 shrink-0"></i>
                    <span class="hidden lg:inline">Data Petugas</span>
                </button>
                <button @click="navigateToProfile()"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-medium text-slate-300 transition-all duration-200 hover:bg-white/5 hover:text-white lg:justify-start" title="Pengaturan">
                    <i data-lucide="settings" class="h-5 w-5 shrink-0"></i>
                    <span class="hidden lg:inline">Pengaturan</span>
                </button>
            </div>
        </nav>

        <div class="border-t border-white/5 p-4">
            <div class="rounded-2xl bg-white/[0.06] border border-white/5 p-2 lg:p-3 hover:bg-white/10 transition">
                <button @click="navigateToProfile()" class="flex w-full items-center gap-3 text-left">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-sm font-black text-white shadow-sm" x-text="role === 'admin' ? 'AD' : currentUser.initial"></div>
                    <div class="hidden min-w-0 flex-1 lg:block">
                        <p class="truncate text-sm font-bold text-white" x-text="role === 'admin' ? 'Administrator' : currentUser.nama"></p>
                        <p class="text-[11px] font-medium text-emerald-400/80">Administrator</p>
                    </div>
                    <i data-lucide="chevron-right" class="hidden h-4 w-4 text-emerald-300/50 lg:block"></i>
                </button>
            </div>
            <button @click="logout()" class="mt-2.5 flex w-full items-center justify-center gap-3 rounded-xl px-2 py-2.5 text-xs font-bold text-slate-400 transition hover:bg-red-500/10 hover:text-red-300 lg:px-4" title="Keluar">
                <i data-lucide="log-out" class="h-4 w-4"></i>
                <span class="hidden lg:inline">Keluar</span>
            </button>
        </div>
    </aside>

    <!-- PERMANENT RESIDENT SIDEBAR -->
    <aside x-show="isLoggedIn && role === 'warga' && currentPage !== 'landing'"
           class="fixed inset-y-0 left-0 z-40 flex w-20 flex-col border-r border-[#153427] bg-[#0b2118] shadow-2xl shadow-emerald-950/40 lg:w-72">
        <div class="flex h-24 items-center justify-center border-b border-white/5 lg:px-7 lg:justify-start">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-lg shadow-emerald-900/40">
                    <i data-lucide="shield-check" class="h-5 w-5"></i>
                </div>
                <div class="hidden lg:block">
                    <p class="text-lg font-black tracking-tight text-white">E-REPORT</p>
                    <p class="text-[9px] font-bold uppercase tracking-[0.24em] text-emerald-400/70">Portal Warga</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Quick Search (Reference Style) -->
        <div class="hidden lg:block px-4 pt-5 pb-2">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3.5 top-3 h-3.5 w-3.5 text-emerald-300/40"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari laporan..." class="w-full rounded-xl bg-white/5 border border-white/10 py-2 pl-9 pr-3 text-xs text-white placeholder:text-emerald-200/40 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500/50 transition">
            </div>
        </div>

        <nav class="flex-1 px-2 py-4 lg:px-4">
            <p class="hidden px-4 pb-3 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400/60 lg:block">Menu Utama</p>
            <div class="space-y-1.5">
                <button @click="currentPage = 'warga-dashboard'"
                        :class="currentPage === 'warga-dashboard' ? 'bg-[#163a2c] text-emerald-300 border border-emerald-500/20 shadow-md shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Dashboard Warga">
                    <i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0"></i><span class="hidden lg:inline">Dashboard Warga</span>
                </button>
                <button @click="currentPage = 'warga-buat'"
                        :class="currentPage === 'warga-buat' ? 'bg-[#163a2c] text-emerald-300 border border-emerald-500/20 shadow-md shadow-emerald-950/20 font-bold' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Buat Pengaduan Baru">
                    <i data-lucide="circle-plus" class="h-5 w-5 shrink-0"></i><span class="hidden lg:inline">Buat Pengaduan Baru</span>
                </button>
                <button @click="currentPage = 'warga-dashboard'; filterStatus = 'all'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-medium text-slate-300 transition-all duration-200 hover:bg-white/5 hover:text-white lg:justify-start" title="Riwayat Laporan Saya">
                    <i data-lucide="history" class="h-5 w-5 shrink-0"></i><span class="hidden lg:inline">Riwayat Laporan Saya</span>
                </button>
                <button @click="showToast('Pusat bantuan akan dihubungi oleh petugas layanan.', 'info', 'life-buoy')"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-medium text-slate-300 transition-all duration-200 hover:bg-white/5 hover:text-white lg:justify-start" title="Bantuan">
                    <i data-lucide="circle-help" class="h-5 w-5 shrink-0"></i><span class="hidden lg:inline">Bantuan</span>
                </button>
                <button @click="navigateToProfile()"
                        :class="currentPage === 'profil' ? 'bg-[#163a2c] text-emerald-300' : 'text-slate-300 hover:bg-white/5 hover:text-white font-medium'"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl px-3.5 py-3 text-sm transition-all duration-200 lg:justify-start" title="Pengaturan Profil">
                    <i data-lucide="settings" class="h-5 w-5 shrink-0"></i><span class="hidden lg:inline">Pengaturan Profil</span>
                </button>
            </div>
        </nav>
        <div class="border-t border-white/5 p-4">
            <button @click="navigateToProfile()" class="flex w-full items-center gap-3 rounded-2xl bg-white/[0.06] border border-white/5 p-2 text-left lg:p-3 hover:bg-white/10 transition">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 text-sm font-black text-white shadow-sm" x-text="currentUser.initial"></div>
                <div class="hidden min-w-0 flex-1 lg:block">
                    <p class="truncate text-sm font-bold text-white" x-text="currentUser.nama"></p>
                    <p class="text-[11px] font-medium text-emerald-400/80">Warga Terdaftar</p>
                </div>
                <i data-lucide="chevron-right" class="hidden h-4 w-4 text-emerald-300/50 lg:block"></i>
            </button>
            <button @click="logout()" class="mt-2.5 flex w-full items-center justify-center gap-3 rounded-xl px-2 py-2.5 text-xs font-bold text-slate-400 transition hover:bg-red-500/10 hover:text-red-300 lg:px-4">
                <i data-lucide="log-out" class="h-4 w-4"></i><span class="hidden lg:inline">Keluar</span>
            </button>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-grow" :class="isLoggedIn && (role === 'admin' || role === 'warga') && currentPage !== 'landing' ? 'ml-20 lg:ml-72' : ''">

        <!-- PAGE 1: LANDING PAGE -->
        <section x-show="currentPage === 'landing'" class="hero-glow py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

                <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 mb-6 border border-emerald-200/80 shadow-sm animate-pulse-ring">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-2 text-emerald-600"></i> Platform Resmi Penanganan Pengaduan Terpadu 24/7
                </span>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-slate-900 tracking-tight max-w-5xl mx-auto leading-[1.15] sm:leading-[1.1]">
                    Lapor Kendala Lingkungan <br class="hidden sm:block"> Lebih Transparan & <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-700 via-emerald-500 to-teal-600">Cepat Ditangani</span>
                </h1>

                <p class="mt-6 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed font-normal">
                    Sistem informasi pengaduan masyarakat berbasis digital. Pantau progres tindak lanjut petugas lapangan secara langsung dengan transparansi penuh.
                </p>

                <div class="mt-9 flex flex-col sm:flex-row justify-center gap-4 max-w-md mx-auto">
                    <button @click="isLoggedIn ? currentPage = 'warga-buat' : currentPage = 'register'" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-8 py-4 rounded-2xl font-black text-sm shadow-xl shadow-emerald-600/25 hover:shadow-2xl hover:scale-[1.02] transition duration-200 flex items-center justify-center space-x-2">
                        <span>Buat Pengaduan Sekarang</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </button>
                    <button @click="isLoggedIn ? currentPage = 'warga-dashboard' : currentPage = 'login'" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 px-8 py-4 rounded-2xl font-bold text-sm shadow-sm hover:shadow-md hover:border-slate-300 transition duration-200">
                        Lihat Laporan Warga
                    </button>
                </div>

                <!-- Interactive Stats Bar (Modul 8 - Public Analytics) -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 sm:gap-6 mt-16 max-w-6xl mx-auto">
                    <div class="rounded-[26px] bg-white border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300 text-left">
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-3.5 shadow-sm">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div class="text-3xl font-black text-slate-900 tracking-tight" x-text="reports.length"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Total Pengaduan</div>
                    </div>

                    <div class="rounded-[26px] bg-white border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 hover:border-amber-200 transition-all duration-300 text-left">
                        <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-3.5 shadow-sm">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <div class="text-3xl font-black text-amber-600 tracking-tight" x-text="reports.filter(r => r.status === 'Pending').length"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Verifikasi (Pending)</div>
                    </div>

                    <div class="rounded-[26px] bg-white border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 hover:border-teal-200 transition-all duration-300 text-left">
                        <div class="w-11 h-11 bg-teal-50 text-teal-700 rounded-2xl flex items-center justify-center mb-3.5 shadow-sm">
                            <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                        </div>
                        <div class="text-3xl font-black text-teal-700 tracking-tight" x-text="reports.filter(r => r.status === 'Proses').length"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Dalam Penanganan</div>
                    </div>

                    <div class="rounded-[26px] bg-white border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300 text-left">
                        <div class="w-11 h-11 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-3.5 shadow-sm">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                        </div>
                        <div class="text-3xl font-black text-emerald-600 tracking-tight" x-text="reports.filter(r => r.status === 'Selesai').length"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Telah Dituntaskan</div>
                    </div>

                    <div class="rounded-[26px] bg-white border border-slate-200/80 p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 hover:border-emerald-200 transition-all duration-300 col-span-2 md:col-span-1 text-left">
                        <div class="w-11 h-11 bg-emerald-50 text-emerald-700 rounded-2xl flex items-center justify-center mb-3.5 shadow-sm">
                            <i data-lucide="percent" class="w-5 h-5"></i>
                        </div>
                        <div class="text-3xl font-black text-emerald-700 tracking-tight" x-text="completionRate + '%'"></div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1.5">Tingkat Penyelesaian</div>
                    </div>
                </div>

                <!-- Visual Workflow Cards -->
                <div class="mt-20 border-t border-slate-200/60 pt-16">
                    <h2 class="text-xs font-extrabold text-emerald-700 uppercase tracking-widest mb-3">Cara Kerja Sistem</h2>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mb-10 tracking-tight">Alur Penyelesaian Laporan</h3>

                    <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto text-left">
                        <div class="bg-white p-7 rounded-[28px] border border-slate-200/80 shadow-[0_4px_25px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1.5 hover:border-emerald-200/80 transition-all duration-300 relative group">
                            <div class="w-11 h-11 bg-[#0b2118] text-white rounded-2xl flex items-center justify-center font-black text-sm mb-5 shadow-sm">1</div>
                            <h4 class="font-extrabold text-slate-900 text-lg mb-2">Unggah Pengaduan</h4>
                            <p class="text-xs text-slate-500 leading-relaxed font-normal">Tulis rincian masalah lingkungan beserta lokasi dan foto bukti lampiran pendukung.</p>
                        </div>
                        <div class="bg-white p-7 rounded-[28px] border border-slate-200/80 shadow-[0_4px_25px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1.5 hover:border-emerald-200/80 transition-all duration-300 relative group">
                            <div class="w-11 h-11 bg-emerald-600 text-white rounded-2xl flex items-center justify-center font-black text-sm mb-5 shadow-sm">2</div>
                            <h4 class="font-extrabold text-slate-900 text-lg mb-2">Verifikasi & Dispatch</h4>
                            <p class="text-xs text-slate-500 leading-relaxed font-normal">Petugas admin memverifikasi laporan lalu menginstruksikan tim teknis di lapangan.</p>
                        </div>
                        <div class="bg-white p-7 rounded-[28px] border border-slate-200/80 shadow-[0_4px_25px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1.5 hover:border-emerald-200/80 transition-all duration-300 relative group">
                            <div class="w-11 h-11 bg-teal-600 text-white rounded-2xl flex items-center justify-center font-black text-sm mb-5 shadow-sm">3</div>
                            <h4 class="font-extrabold text-slate-900 text-lg mb-2">Tindak Lanjut & Selesai</h4>
                            <p class="text-xs text-slate-500 leading-relaxed font-normal">Masalah diselesaikan oleh petugas dan hasil penanganan diunggah ke sistem secara publik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 7: ADMIN REPORT MANAGEMENT -->
        <section x-show="currentPage === 'admin-pengaduan'" class="min-h-screen bg-slate-50/70 px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-[1440px]">
                <header class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <p class="mb-1 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-700">Manajemen Operasional</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">Kelola Pengaduan</h1>
                        <p class="mt-1 text-xs text-slate-400">Tinjau, filtrasi, dan lanjutkan penanganan laporan warga.</p>
                    </div>
                    <button @click="exportReportsCsv()" class="flex h-11 items-center justify-center gap-2 rounded-xl bg-[#0b2118] px-4 text-xs font-bold text-white transition hover:bg-emerald-700">
                        <i data-lucide="download" class="h-4 w-4"></i> Ekspor Data
                    </button>
                </header>

                <div class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'border-[#0b2118] bg-[#0b2118] text-white' : 'border-slate-200 bg-white text-slate-600'" class="rounded-2xl border p-4 text-left transition">
                        <span class="block text-[10px] font-bold uppercase tracking-wider opacity-70">Semua</span>
                        <span class="mt-1 block text-2xl font-black" x-text="reports.length"></span>
                    </button>
                    <button @click="filterStatus = 'Pending'" :class="filterStatus === 'Pending' ? 'border-amber-500 bg-amber-500 text-white' : 'border-slate-200 bg-white text-slate-600'" class="rounded-2xl border p-4 text-left transition">
                        <span class="block text-[10px] font-bold uppercase tracking-wider opacity-70">Pending</span>
                        <span class="mt-1 block text-2xl font-black" x-text="reports.filter(r => r.status === 'Pending').length"></span>
                    </button>
                    <button @click="filterStatus = 'Proses'" :class="filterStatus === 'Proses' ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 bg-white text-slate-600'" class="rounded-2xl border p-4 text-left transition">
                        <span class="block text-[10px] font-bold uppercase tracking-wider opacity-70">Proses</span>
                        <span class="mt-1 block text-2xl font-black" x-text="reports.filter(r => r.status === 'Proses').length"></span>
                    </button>
                    <button @click="filterStatus = 'Selesai'" :class="filterStatus === 'Selesai' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-200 bg-white text-slate-600'" class="rounded-2xl border p-4 text-left transition">
                        <span class="block text-[10px] font-bold uppercase tracking-wider opacity-70">Selesai</span>
                        <span class="mt-1 block text-2xl font-black" x-text="reports.filter(r => r.status === 'Selesai').length"></span>
                    </button>
                </div>

                <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm">
                    <div class="grid gap-3 border-b border-slate-100 p-5 md:grid-cols-[1fr_220px_180px]">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
                            <input x-model="searchQuery" type="search" placeholder="Cari ID, pelapor, lokasi, atau judul..." class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-3 text-xs outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                        <select x-model="filterCategory" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-600 outline-none">
                            <option value="all">Semua kategori</option>
                            <option>Infrastruktur</option>
                            <option>Kebersihan</option>
                            <option>Fasilitas Publik</option>
                        </select>
                        <button @click="filterStatus = 'all'; filterCategory = 'all'; searchQuery = ''" class="rounded-xl border border-slate-200 text-xs font-bold text-slate-500 hover:bg-slate-50">Reset Filter</button>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[1020px] text-left text-xs">
                            <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Pengaduan</th>
                                    <th class="px-6 py-4">Pelapor &amp; Lokasi</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in filteredReports" :key="item.id">
                                    <tr class="group transition hover:bg-emerald-50/40">
                                        <td class="px-6 py-5"><span class="block font-mono font-black text-emerald-700" x-text="item.id"></span><span class="mt-1 block max-w-xs truncate font-bold text-slate-900" x-text="item.judul"></span></td>
                                        <td class="px-6 py-5"><span class="block font-bold text-slate-800" x-text="item.pelapor"></span><span class="mt-1 block max-w-[220px] truncate text-[11px] text-slate-400" x-text="item.lokasi"></span></td>
                                        <td class="px-6 py-5 text-slate-600" x-text="item.kategori"></td>
                                        <td class="px-6 py-5 text-slate-500" x-text="item.tanggal"></td>
                                        <td class="px-6 py-5"><span class="rounded-full px-3 py-1 text-[10px] font-black uppercase" :class="getStatusBadgeClass(item.status)" x-text="item.status"></span></td>
                                        <td class="px-6 py-5">
                                            <div class="flex justify-end gap-2">
                                                <button @click="viewDetail(item)" class="rounded-xl border border-slate-200 bg-white px-3 py-2 font-bold text-slate-600 hover:border-emerald-300 hover:text-emerald-700">Buka</button>
                                                <button x-show="item.status === 'Pending'" @click="updateReportStatus(item, 'Proses')" class="rounded-xl bg-teal-600 px-3 py-2 font-bold text-white hover:bg-teal-700">Proseskan</button>
                                                <button x-show="item.status === 'Proses'" @click="updateReportStatus(item, 'Selesai')" class="rounded-xl bg-emerald-600 px-3 py-2 font-bold text-white hover:bg-emerald-700">Selesaikan</button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div x-show="filteredReports.length === 0" class="px-6 py-16 text-center">
                        <i data-lucide="search-x" class="mx-auto h-8 w-8 text-slate-300"></i>
                        <p class="mt-3 text-sm font-bold text-slate-500">Tidak ada pengaduan yang cocok</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 8: OFFICER MANAGEMENT -->
        <section x-show="currentPage === 'admin-petugas'" class="min-h-screen bg-slate-50/70 px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-[1440px]">
                <header class="mb-7 flex flex-col justify-between gap-4 md:flex-row md:items-center">
                    <div>
                        <p class="mb-1 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-700">Sumber Daya Lapangan</p>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">Data Petugas</h1>
                        <p class="mt-1 text-xs text-slate-400">Kelola status petugas dan penugasan laporan.</p>
                    </div>
                    <button @click="showOfficerForm = !showOfficerForm" class="flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white shadow-md shadow-emerald-600/20 hover:bg-emerald-700">
                        <i data-lucide="user-plus" class="h-4 w-4"></i> Tambah Petugas
                    </button>
                </header>

                <div x-show="showOfficerForm" class="mb-5 rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="grid gap-3 md:grid-cols-[1fr_1fr_200px_auto]">
                        <input x-model="officerForm.nama" placeholder="Nama petugas" class="rounded-xl border border-slate-200 px-4 py-3 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10">
                        <input x-model="officerForm.bidang" placeholder="Bidang penanganan" class="rounded-xl border border-slate-200 px-4 py-3 text-xs outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10">
                        <select x-model="officerForm.status" class="rounded-xl border border-slate-200 px-4 py-3 text-xs"><option>Aktif</option><option>Nonaktif</option></select>
                        <button @click="addOfficer()" class="rounded-xl bg-[#0b2118] px-5 text-xs font-bold text-white hover:bg-emerald-800 transition">Simpan</button>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[900px] text-left text-xs">
                            <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <tr><th class="px-6 py-4">Petugas</th><th class="px-6 py-4">Bidang</th><th class="px-6 py-4">Tugas Aktif</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Pengaturan</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="officer in officers" :key="officer.id">
                                    <tr class="transition hover:bg-slate-50/70">
                                        <td class="px-6 py-5"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0b2118] text-xs font-black text-white" x-text="officer.initials"></div><div><p class="font-bold text-slate-900" x-text="officer.nama"></p><p class="mt-1 text-[10px] text-slate-400" x-text="officer.email"></p></div></div></td>
                                        <td class="px-6 py-5 font-semibold text-slate-600" x-text="officer.bidang"></td>
                                        <td class="px-6 py-5"><span class="rounded-lg bg-teal-50 px-2.5 py-1 font-black text-teal-700" x-text="officer.tugas + ' laporan'"></span></td>
                                        <td class="px-6 py-5"><button @click="officer.status = officer.status === 'Aktif' ? 'Nonaktif' : 'Aktif'" class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-black" :class="officer.status === 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'"><span class="h-1.5 w-1.5 rounded-full" :class="officer.status === 'Aktif' ? 'bg-emerald-500' : 'bg-slate-400'"></span><span x-text="officer.status"></span></button></td>
                                        <td class="px-6 py-5 text-right"><select @change="assignOfficer(officer, $event.target.value)" class="rounded-xl border border-slate-200 px-3 py-2 text-[11px] font-semibold text-slate-600"><option value="">Atur tugas laporan</option><template x-for="report in reports.filter(r => r.status !== 'Selesai')" :key="report.id"><option :value="report.id" x-text="report.id + ' - ' + report.judul"></option></template></select></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>

        <!-- PAGE 2: AUTH - REGISTER -->
        <section x-show="currentPage === 'register'" class="py-12 max-w-2xl mx-auto px-4">
            <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-2xl">
                <div class="text-center mb-8">
                    <div class="inline-flex p-3 bg-brand-50 text-brand-600 rounded-2xl mb-3"><i data-lucide="user-plus" class="w-6 h-6"></i></div>
                    <h2 class="text-2xl font-black text-slate-900">Registrasi Warga Baru</h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi data identitas untuk membuat akun E-Report.</p>
                </div>
                <form @submit.prevent="registerCitizen()" class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">NIK (16 digit)</label>
                        <input type="text" inputmode="numeric" maxlength="16" x-model="registration.nik" required pattern="[0-9]{16}" class="form-input" placeholder="3201xxxxxxxxxxxx">
                        <p class="text-[10px] text-red-500 mt-1" x-show="registration.nik.length > 0 && registration.nik.length !== 16">NIK harus terdiri dari tepat 16 digit.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" x-model="registration.nama" required minlength="3" class="form-input" placeholder="Nama sesuai KTP">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" x-model="registration.email" required class="form-input" placeholder="nama@email.com">
                    </div>
                    <div>
                        <label class="form-label">Nomor HP</label>
                        <input type="tel" x-model="registration.phone" required class="form-input" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Kata Sandi</label>
                        <input type="password" x-model="registration.password" required minlength="8" class="form-input" placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label class="form-label">Kelurahan</label>
                        <select x-model="registration.kelurahan" required class="form-input">
                            <option value="">Pilih Kelurahan</option>
                            <option>Sukamaju</option><option>Sukaraja</option><option>Cempaka</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">RT / RW</label>
                        <input type="text" x-model="registration.rtRw" required class="form-input" placeholder="RT 03 / RW 04">
                    </div>
                    <button type="submit" class="sm:col-span-2 bg-brand-600 hover:bg-brand-700 text-white py-3.5 rounded-xl text-xs font-extrabold shadow-lg">Daftar Sekarang</button>
                </form>
                <p class="text-center text-xs text-slate-400 mt-6">Sudah punya akun? <button @click="currentPage = 'login'" class="text-brand-600 font-bold">Masuk</button></p>
            </div>
        </section>

        <!-- PAGE 2: AUTH - LOGIN -->
        <section x-show="currentPage === 'login'" class="py-16 flex justify-center items-center px-4">
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-2xl w-full max-w-md relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-500/10 rounded-bl-full pointer-events-none"></div>

                <div class="text-center mb-8">
                    <div class="inline-flex p-3 bg-brand-50 text-brand-600 rounded-2xl mb-3 shadow-sm border border-brand-100">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900">Selamat Datang</h2>
                    <p class="text-xs text-slate-400 mt-1">Masuk untuk mengelola & memantau pengaduan</p>
                </div>

                <form @submit.prevent="login()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email / NIK</label>
                        <input type="text" x-model="loginForm.email" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-none text-xs font-medium transition" placeholder="warga@example.com atau 3201xxxx">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <input type="password" x-model="loginForm.password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-none text-xs font-medium transition" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mode Sesi Login</label>
                        <select x-model="role" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-none text-xs bg-white font-bold text-slate-800">
                            <option value="warga">Masyarakat (Warga)</option>
                            <option value="admin">Administrator / Petugas Lapangan</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-emerald-600/20 transition text-xs uppercase tracking-wider">
                        Masuk ke Dashboard
                    </button>
                </form>

                <div class="mt-8 text-center text-xs text-slate-400 border-t border-slate-100 pt-6">
                    Belum memiliki akun resmi? <a @click="currentPage = 'register'" class="text-brand-600 font-extrabold cursor-pointer hover:underline">Registrasi Warga Baru</a>
                </div>
            </div>
        </section>

        <!-- PAGE 4: WARGA DASHBOARD (Modul 7 - Multi-Criteria Filter & Search) -->
        <section x-show="currentPage === 'warga-dashboard'" class="min-h-screen bg-[#f8fafc] px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-[1440px]">

                <!-- WELCOME BANNER & HEADER (HORIZONTAL MODERN) -->
                <header class="mb-7 flex flex-col justify-between gap-5 rounded-[26px] border border-slate-200/80 bg-white px-7 py-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] md:flex-row md:items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-lg shadow-emerald-600/20">
                            <i data-lucide="sparkles" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Welcome <span x-text="currentUser.nama"></span> !</h1>
                                <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/70 px-3 py-1 text-[11px] font-bold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Warga Aktif
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 font-medium">Selamat Datang di Portal Warga • Pantau perkembangan pengaduan Anda dalam satu tempat.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-300 hover:text-emerald-700 hover:bg-slate-50" title="Notifikasi">
                            <i data-lucide="bell" class="h-5 w-5"></i><span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full border-2 border-white bg-red-500"></span>
                        </button>
                        <button @click="currentPage = 'warga-buat'" class="flex h-11 items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition duration-200">
                            <i data-lucide="plus" class="h-4 w-4"></i>Buat Pengaduan
                        </button>
                    </div>
                </header>

                <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-3">
                    <article class="group rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                        <div class="flex items-start justify-between">
                            <div><p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p><p class="mt-3 text-4xl font-black text-slate-900 tracking-tight" x-text="myReports.filter(r => r.status === 'Pending').length"></p><p class="mt-2 text-xs font-semibold text-amber-600">Belum diverifikasi</p></div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"><i data-lucide="clock-3" class="h-5 w-5"></i></div>
                        </div>
                    </article>
                    <article class="group rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] transition hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg">
                        <div class="flex items-start justify-between">
                            <div><p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Laporan Diproses</p><p class="mt-3 text-4xl font-black text-slate-900 tracking-tight" x-text="myReports.filter(r => r.status === 'Proses').length"></p><p class="mt-2 text-xs font-semibold text-teal-700">Sedang ditangani</p></div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-700"><i data-lucide="truck" class="h-5 w-5"></i></div>
                        </div>
                    </article>
                    <article class="group rounded-[26px] border border-[#1b3d2f] bg-[#0c241b] text-white p-6 shadow-xl shadow-emerald-950/15 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-emerald-500/40">
                        <div class="flex items-start justify-between">
                            <div><p class="text-[11px] font-black uppercase tracking-wider text-emerald-300/80">Selesai Dituntaskan</p><p class="mt-3 text-4xl font-black text-emerald-400 tracking-tight" x-text="myReports.filter(r => r.status === 'Selesai').length"></p><p class="mt-2 text-xs font-semibold text-emerald-200/70">Pengaduan selesai</p></div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-400"><i data-lucide="circle-check-big" class="h-5 w-5"></i></div>
                        </div>
                    </article>
                </div>

                <div class="overflow-hidden rounded-[26px] border border-slate-200/80 bg-white shadow-[0_4px_25px_rgba(0,0,0,0.03)]">
                    <div class="flex flex-col justify-between gap-4 border-b border-slate-100 px-6 py-5 md:flex-row md:items-center">
                        <div><h2 class="text-base font-black text-slate-900">Riwayat Laporan Saya</h2><p class="mt-1 text-xs text-slate-400 font-medium">Daftar pengaduan yang telah Anda kirimkan.</p></div>
                        <div class="relative w-full md:w-72">
                            <i data-lucide="search" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
                            <input type="search" x-model="searchQuery" placeholder="Cari ID atau judul laporan..." class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-3 text-xs font-medium outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[820px] text-left text-xs">
                            <thead class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400"><tr><th class="px-6 py-4">ID Laporan</th><th class="px-6 py-4">Judul</th><th class="px-6 py-4">Kategori</th><th class="px-6 py-4">Tanggal</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Aksi</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in filteredReports.filter(r => r.pelapor === currentUser.nama)" :key="item.id">
                                    <tr class="transition hover:bg-emerald-50/30">
                                        <td class="whitespace-nowrap px-6 py-5 font-mono font-black text-emerald-700" x-text="item.id"></td>
                                        <td class="max-w-xs px-6 py-5 font-bold text-slate-900"><p class="truncate" x-text="item.judul"></p></td>
                                        <td class="px-6 py-5 text-slate-600" x-text="item.kategori"></td>
                                        <td class="whitespace-nowrap px-6 py-5 text-slate-500" x-text="item.tanggal"></td>
                                        <td class="px-6 py-5"><span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase" :class="getStatusBadgeClass(item.status)" x-text="item.status"></span></td>
                                        <td class="px-6 py-5 text-right"><button @click="viewDetail(item)" class="rounded-xl border border-slate-200 px-3 py-2 font-bold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">Lihat Detail</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div x-show="filteredReports.filter(r => r.pelapor === currentUser.nama).length === 0" class="px-6 py-14 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i data-lucide="inbox" class="h-7 w-7"></i></div>
                        <p class="mt-4 text-sm font-bold text-slate-700">Belum ada laporan yang ditemukan</p>
                        <p class="mt-1 text-xs text-slate-400">Coba kata kunci lain atau buat pengaduan baru.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAGE 5: FORM BUAT PENGADUAN (Modul 2, 3, 4 - Incident, Geo Location & Media Processing) -->
        <section x-show="currentPage === 'warga-buat'" class="py-8 max-w-3xl mx-auto px-4">
            <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">

                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                    <button @click="currentPage = 'warga-dashboard'" class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 transition">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-black text-slate-900">Formulir Pengaduan Warga</h1>
                        <p class="text-xs text-slate-400">Lengkapi klasifikasi insiden, tagging lokasi, dan bukti media visual.</p>
                    </div>
                </div>

                <form @submit.prevent="submitReport()" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Judul Pengaduan</label>
                        <input type="text" x-model="newReport.judul" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Contoh: Lampu Penerangan Jalan Padam di RT 02">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Permasalahan (Modul 2)</label>
                            <select x-model="newReport.kategori" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-bold bg-white text-slate-800 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                                <option value="Infrastruktur">Infrastruktur & Jalan</option>
                                <option value="Kebersihan">Kebersihan & Sampah</option>
                                <option value="Fasilitas Publik">Fasilitas Publik & PJU</option>
                                <option value="Keamanan">Keamanan & Ketertiban</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Wilayah / RT / RW (Modul 3)</label>
                            <input type="text" x-model="newReport.rtRw" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="RT 03 / RW 04 Kel. Sukamaju">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lokasi Detail Kejadian</label>
                        <input type="text" x-model="newReport.lokasi" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Jl. Anggrek No. 12 (Patokan dekat Masjid Al-Ikhlas)">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Masalah Lengkap</label>
                        <textarea x-model="newReport.deskripsi" rows="4" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Jelaskan kondisi detail kejadian, waktu terjadinya kendala, dan dampaknya..."></textarea>
                    </div>

                    <!-- File Image Upload Preview Box (Modul 4) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Unggah Foto Bukti Kejadian (Modul 4 - Media Processing)</label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-2xl p-6 text-center transition cursor-pointer bg-slate-50/50 relative">
                            <input type="file" accept="image/*" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <template x-if="!newReport.foto">
                                <div>
                                    <i data-lucide="upload-cloud" class="w-10 h-10 text-brand-500 mx-auto mb-2"></i>
                                    <span class="text-xs font-bold text-slate-700 block">Klik atau seret foto ke area ini</span>
                                    <span class="text-[10px] text-slate-400">Format JPG, PNG (Maksimal 5MB)</span>
                                </div>
                            </template>
                            <template x-if="newReport.foto">
                                <div class="flex items-center justify-center space-x-4">
                                    <img :src="newReport.foto" class="w-20 h-20 object-cover rounded-xl border border-slate-200">
                                    <div class="text-left">
                                        <span class="text-xs font-bold text-emerald-600 block">Foto Berhasil Diunggah!</span>
                                        <span class="text-[10px] text-slate-400">Klik untuk mengganti gambar</span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="currentPage = 'warga-dashboard'" class="px-6 py-3 border border-slate-300 text-slate-700 font-bold rounded-xl text-xs hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                            Kirimkan Pengaduan
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- PAGE 6: ADMIN DASHBOARD -->
        <section x-show="currentPage === 'admin-dashboard'" class="min-h-screen bg-[#f8fafc] px-5 py-8 sm:px-8 lg:px-10">
            <div class="mx-auto max-w-[1440px]">

                <!-- WELCOME BANNER & HEADER (HORIZONTAL MODERN) -->
                <header class="mb-7 flex flex-col justify-between gap-5 rounded-[26px] border border-slate-200/80 bg-white px-7 py-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] md:flex-row md:items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white shadow-lg shadow-emerald-600/20">
                            <i data-lucide="sparkles" class="h-6 w-6"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Welcome Administrator !</h1>
                                <span class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-200/70 px-3 py-1 text-[11px] font-bold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Sistem Aktif 24/7
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 font-medium">E-REPORT Control Center • Pantau dan tangani seluruh pengaduan warga secara terpadu.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-300 hover:text-emerald-700 hover:bg-slate-50" title="Notifikasi">
                            <i data-lucide="bell" class="h-5 w-5"></i>
                            <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full border-2 border-white bg-red-500"></span>
                        </button>
                        <button @click="exportReportsCsv()" class="flex h-11 items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-5 text-xs font-bold text-white shadow-md shadow-emerald-600/20 transition duration-200">
                            <i data-lucide="download" class="h-4 w-4"></i>
                            Ekspor CSV
                        </button>
                    </div>
                </header>

                <!-- Admin Stats Summary -->
                <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <article class="group rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Menunggu Konfirmasi</p>
                                <p class="mt-3 text-4xl font-black text-slate-900 tracking-tight" x-text="reports.filter(r => r.status === 'Pending').length">0</p>
                                <p class="mt-2 text-xs font-semibold text-amber-600">Perlu ditinjau</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                                <i data-lucide="clock-3" class="h-5 w-5"></i>
                            </div>
                        </div>
                    </article>
                    <article class="group rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)] transition hover:-translate-y-1 hover:border-teal-200 hover:shadow-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Sedang Ditangani</p>
                                <p class="mt-3 text-4xl font-black text-slate-900 tracking-tight" x-text="reports.filter(r => r.status === 'Proses').length">0</p>
                                <p class="mt-2 text-xs font-semibold text-teal-700">Dalam penanganan</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                                <i data-lucide="truck" class="h-5 w-5"></i>
                            </div>
                        </div>
                    </article>
                    <article class="group rounded-[26px] border border-[#1b3d2f] bg-[#0c241b] text-white p-6 shadow-xl shadow-emerald-950/15 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:border-emerald-500/40">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-300/80">Selesai</p>
                                <p class="mt-3 text-4xl font-black text-emerald-400 tracking-tight" x-text="reports.filter(r => r.status === 'Selesai').length">0</p>
                                <p class="mt-2 text-xs font-semibold text-emerald-200/70">Selesai dituntaskan</p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-400">
                                <i data-lucide="circle-check-big" class="h-5 w-5"></i>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="mb-8 grid grid-cols-1 gap-5 xl:grid-cols-[1.55fr_0.45fr]">
                    <div class="rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_4px_25px_rgba(0,0,0,0.03)]">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="font-black text-slate-900 text-base">Tren Pengaduan</h2>
                                <p class="mt-1 text-xs text-slate-400">Pergerakan laporan masuk enam bulan terakhir</p>
                            </div>
                            <span class="rounded-full bg-emerald-50 border border-emerald-200/70 px-3 py-1 text-[10px] font-black text-emerald-700">+18,4%</span>
                        </div>
                        <div class="mt-8 flex h-52 items-end gap-3 sm:gap-5" aria-label="Grafik tren pengaduan bulanan">
                            <template x-for="month in [{name:'Jun', value:42},{name:'Jul', value:58},{name:'Ags', value:48},{name:'Sep', value:72},{name:'Okt', value:64},{name:'Nov', value:88}]" :key="month.name">
                                <div class="flex flex-1 flex-col items-center gap-3">
                                    <div class="flex w-full items-end justify-center rounded-t-xl bg-emerald-50/50" style="height: 170px">
                                        <div class="w-full max-w-10 rounded-t-xl bg-gradient-to-t from-emerald-700 to-emerald-400 shadow-lg shadow-emerald-500/10 transition-all duration-500 hover:from-[#0b2118] hover:to-emerald-500" :style="'height:' + month.value + '%'"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400" x-text="month.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="rounded-[26px] bg-[#0c241b] border border-[#1a3d2e] p-6 text-white shadow-xl shadow-emerald-950/15">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10">
                            <i data-lucide="gauge" class="h-6 w-6 text-emerald-300"></i>
                        </div>
                        <p class="mt-8 text-xs font-bold uppercase tracking-wider text-emerald-300/70">Tingkat Penyelesaian</p>
                        <p class="mt-2 text-4xl font-black text-white tracking-tight" x-text="completionRate + '%'"></p>
                        <p class="mt-3 text-xs leading-5 text-emerald-100/70">Pengaduan terselesaikan dari seluruh laporan yang tercatat.</p>
                        <div class="mt-6 h-2.5 rounded-full bg-white/10">
                            <div class="h-2.5 rounded-full bg-emerald-400 transition-all duration-500 shadow-sm" :style="'width:' + completionRate + '%'"></div>
                        </div>
                    </div>
                </div>

                <!-- Admin Table Box -->
                <div class="overflow-hidden rounded-[26px] border border-slate-200/80 bg-white shadow-[0_4px_25px_rgba(0,0,0,0.03)]">
                    <div class="flex flex-col justify-between gap-4 border-b border-slate-100 px-6 py-5 md:flex-row md:items-center">
                        <div>
                            <h2 class="text-base font-black text-slate-900">Kelola Seluruh Pengaduan Warga</h2>
                            <p class="mt-1 text-xs text-slate-400">Daftar terbaru pengaduan yang masuk ke sistem.</p>
                        </div>
                        <div class="relative w-full md:w-72">
                            <i data-lucide="search" class="absolute left-3.5 top-3 h-4 w-4 text-slate-400"></i>
                            <input type="text" x-model="searchQuery" placeholder="Cari kode atau judul laporan..." class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-3 text-xs font-medium outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[980px] text-left text-xs text-slate-600">
                            <thead class="border-b border-slate-100 bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">ID</th>
                                    <th class="px-6 py-4">Nama Pelapor</th>
                                    <th class="px-6 py-4">Judul Laporan</th>
                                    <th class="px-6 py-4">Kategori</th>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in filteredReports" :key="item.id">
                                    <tr class="transition hover:bg-emerald-50/30">
                                        <td class="whitespace-nowrap px-6 py-4 font-mono font-bold text-emerald-700" x-text="item.id"></td>
                                        <td class="whitespace-nowrap px-6 py-4 font-bold text-slate-900" x-text="item.pelapor"></td>
                                        <td class="max-w-xs px-6 py-4">
                                            <p class="truncate font-semibold text-slate-800" x-text="item.judul"></p>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4" x-text="item.kategori"></td>
                                        <td class="whitespace-nowrap px-6 py-4 text-slate-500" x-text="item.tanggal"></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wider" :class="getStatusBadgeClass(item.status)" x-text="item.status"></span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="viewDetail(item)" class="rounded-xl bg-[#0b2118] px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-[#153a2c]">
                                                Kelola &amp; Respon
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <section class="mt-8">
                    <div class="mb-5 flex items-end justify-between gap-4">
                        <div>
                            <h2 class="text-base font-black text-slate-900">Daftar Pengaduan Warga</h2>
                            <p class="mt-1 text-xs text-slate-400">Tinjau laporan terbaru beserta bukti visual yang dilampirkan.</p>
                        </div>
                        <button @click="currentPage = 'admin-pengaduan'" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 transition">Lihat semua</button>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <template x-for="item in reports" :key="'card-' + item.id">
                            <article class="group overflow-hidden rounded-[26px] border border-slate-200/80 bg-white shadow-[0_4px_25px_rgba(0,0,0,0.03)] transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-emerald-200">
                                <div class="relative h-44 overflow-hidden bg-slate-100">
                                    <img :src="item.foto" :alt="item.judul" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Tidak+Ada+Foto'">
                                    <span class="absolute left-4 top-4 rounded-full px-3 py-1 text-[10px] font-black uppercase shadow-sm" :class="getStatusBadgeClass(item.status)" x-text="item.status"></span>
                                </div>
                                <div class="p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="font-mono text-[10px] font-bold text-emerald-700" x-text="item.id"></span>
                                        <span class="text-[10px] font-bold text-slate-400" x-text="item.tanggal"></span>
                                    </div>
                                    <h3 class="mt-3 line-clamp-1 text-sm font-black text-slate-900" x-text="item.judul"></h3>
                                    <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500" x-text="item.deskripsi"></p>
                                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                        <span class="truncate text-[11px] font-bold text-slate-500" x-text="item.kategori"></span>
                                        <button @click="viewDetail(item)" class="shrink-0 rounded-xl bg-[#0b2118] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#153a2c]">Kelola</button>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>
            </div>
        </section>
        <!-- PAGE 7: DETAIL PENGADUAN & RESPONSE TIMELINE (Modul 5, 6 & 10) -->
        <section x-show="currentPage === 'warga-detail-pengaduan' || currentPage === 'admin-detail-pengaduan'" class="py-8 max-w-4xl mx-auto px-4">
            <template x-if="selectedReport">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden">

                    <!-- Detail Header Bar -->
                    <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="font-mono text-xs font-bold text-slate-400" x-text="selectedReport.id"></span>
                                <span class="text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider" :class="getStatusBadgeClass(selectedReport.status)" x-text="selectedReport.status"></span>
                            </div>
                            <h1 class="text-2xl font-black text-white" x-text="selectedReport.judul"></h1>
                            <p class="text-xs text-slate-400 mt-1" x-text="'Oleh: ' + selectedReport.pelapor + ' • Tanggal: ' + selectedReport.tanggal + ' • ' + (selectedReport.rtRw || 'RT/RW General')"></p>
                        </div>

                        <button @click="currentPage = role === 'admin' ? 'admin-dashboard' : 'warga-dashboard'" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition backdrop-blur-md">
                            Kembali
                        </button>
                    </div>

                    <!-- Detail Content -->
                    <div class="p-6 sm:p-8 space-y-8">

                        <!-- Image & Main Info Grid -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 h-64">
                                <img :src="selectedReport.foto" :alt="selectedReport.judul" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/600x400/e2e8f0/64748b?text=Tidak+Ada+Foto'">
                            </div>

                            <div class="space-y-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori Insiden</span>
                                    <span class="text-sm font-extrabold text-slate-800" x-text="selectedReport.kategori"></span>
                                </div>

                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tagging Lokasi & RT/RW</span>
                                    <span class="text-sm font-extrabold text-slate-800" x-text="selectedReport.lokasi + ' (' + (selectedReport.rtRw || 'RT/RW -') + ')'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Description Box -->
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Pengaduan</h4>
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-700 leading-relaxed font-medium" x-text="selectedReport.deskripsi"></div>
                        </div>

                        <!-- Timeline History & Responses (Modul 5 & 6 Workflow State Engine) -->
                        <div class="border-t border-slate-100 pt-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6">Linimasa Penanganan & Dispatch Petugas (Modul 5 & 6)</h4>

                            <div class="space-y-6 relative before:absolute before:inset-0 before:left-4 before:w-0.5 before:bg-slate-200">
                                <template x-for="item in selectedReport.history" :key="item.id">
                                    <div class="relative flex items-start space-x-4 pl-10">
                                        <div class="absolute left-2 top-0.5 w-4 h-4 rounded-full border-4 border-white shadow-sm"
                                             :class="item.type === 'status' ? 'bg-brand-500' : 'bg-emerald-500'"></div>
                                        <div class="w-full">
                                            <span class="text-[10px] font-bold text-slate-400 block" x-text="item.timestamp"></span>
                                            <h5 class="text-xs font-bold text-slate-900" x-text="item.title"></h5>
                                            <p class="text-[11px] text-slate-500 mt-0.5" x-text="item.description"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <template x-if="selectedReport.fotoSelesai">
                            <div class="border-t border-slate-100 pt-6">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Dokumentasi Bukti Penanganan Selesai</h4>
                                <img :src="selectedReport.fotoSelesai" alt="Bukti penanganan selesai" class="w-full max-h-96 object-cover rounded-2xl border border-slate-200">
                            </div>
                        </template>

                        <!-- Discussion -->
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Komentar / Tanya Jawab Warga</h4>
                                <span class="text-[10px] font-bold text-brand-600" x-text="selectedReport.comments.length + ' diskusi'"></span>
                            </div>
                            <div class="space-y-3 max-h-72 overflow-y-auto custom-scrollbar">
                                <template x-for="comment in selectedReport.comments" :key="comment.id">
                                    <div class="flex gap-3 bg-slate-50 border border-slate-100 rounded-2xl p-4">
                                        <div class="w-8 h-8 shrink-0 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-black" x-text="comment.author.charAt(0)"></div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-extrabold text-slate-800" x-text="comment.author"></span>
                                                <span class="text-[10px] text-slate-400" x-text="comment.timestamp"></span>
                                            </div>
                                            <p class="text-xs text-slate-600 mt-1 leading-relaxed" x-text="comment.message"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <form @submit.prevent="addComment()" class="mt-4 flex gap-3">
                                <input x-model="newComment" required maxlength="500" class="flex-1 px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none" placeholder="Tulis pembaruan atau pertanyaan...">
                                <button type="submit" class="px-5 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold">Kirim</button>
                            </form>
                        </div>

                        <div class="flex justify-end">
                            <button @click="printReport()" class="bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 px-5 py-3 rounded-xl text-xs font-extrabold flex items-center gap-2">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                                Unduh PDF / Cetak Bukti Pengaduan
                            </button>
                        </div>

                        <!-- Admin Action Box (Modul 10 - Administrative Audit & Action Panel) -->
                        <template x-if="role === 'admin'">
                            <div class="bg-slate-900 text-white p-6 sm:p-8 rounded-3xl space-y-4 shadow-xl border border-slate-800 mt-8">
                                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                                    <i data-lucide="sliders" class="w-4 h-4 text-amber-400"></i>
                                    <h4 class="text-xs font-extrabold text-amber-400 uppercase tracking-wider">Aksi Panel Petugas Administrator (Modul 10)</h4>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Ubah Status Pengaduan Workflow</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <button @click="updateStatus('Pending')" :class="selectedReport.status === 'Pending' ? 'bg-amber-500 text-white font-extrabold' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="py-2.5 rounded-xl text-xs transition">Pending</button>
                                        <button @click="updateStatus('Proses')" :class="selectedReport.status === 'Proses' ? 'bg-teal-600 text-white font-extrabold' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="py-2.5 rounded-xl text-xs transition">Proses</button>
                                        <button @click="updateStatus('Selesai')" :class="selectedReport.status === 'Selesai' ? 'bg-emerald-600 text-white font-extrabold' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="py-2.5 rounded-xl text-xs transition">Selesai</button>
                                        <button @click="updateStatus('Ditolak')" :class="selectedReport.status === 'Ditolak' ? 'bg-red-600 text-white font-extrabold' : 'bg-slate-800 text-slate-400 hover:bg-slate-700'" class="py-2.5 rounded-xl text-xs transition">Ditolak</button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tulis Respon / Instruksi Petugas</label>
                                    <textarea x-model="adminTanggapan" rows="3" class="w-full px-4 py-3 text-xs text-slate-900 rounded-xl focus:outline-none font-medium" placeholder="Tuliskan jawaban atau instruksi resmi untuk warga..."></textarea>
                                </div>

                                <div x-show="selectedReport.status === 'Selesai'">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Unggah Foto Bukti Penanganan Selesai</label>
                                    <input type="file" accept="image/*" @change="handleCompletionPhoto($event)" class="w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-600 file:text-white">
                                </div>

                                <button @click="saveTanggapan()" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs py-3.5 rounded-xl font-extrabold transition shadow-lg shadow-emerald-600/20">
                                    Simpan Perubahan & Terbitkan Respon
                                </button>
                            </div>
                        </template>

                    </div>
                </div>
            </template>
        </section>

        <!-- PAGE 8: PROFIL WARGA -->
        <section x-show="currentPage === 'warga-profil' || currentPage === 'admin-profil'" class="py-8 max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="p-6 sm:p-8 bg-slate-900 text-white flex items-center gap-4">
                    <img x-show="currentUser.avatar" :src="currentUser.avatar" alt="Foto profil" class="w-16 h-16 rounded-2xl object-cover">
                    <div x-show="!currentUser.avatar" class="w-16 h-16 bg-gradient-to-tr from-emerald-600 to-teal-700 rounded-2xl flex items-center justify-center font-black text-2xl" x-text="currentUser.initial"></div>
                    <div>
                        <h2 class="text-xl font-black" x-text="currentUser.nama"></h2>
                        <p class="text-xs text-slate-400 font-mono" x-text="'NIK: ' + currentUser.nik"></p>
                    </div>
                </div>

                <div class="px-6 pt-5 border-b border-slate-100 flex gap-1 overflow-x-auto">
                    <button @click="profileTab = 'personal'" :class="profileTab === 'personal' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-400'" class="px-4 py-3 border-b-2 text-xs font-bold whitespace-nowrap">Informasi Pribadi</button>
                    <button @click="profileTab = 'account'" :class="profileTab === 'account' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-400'" class="px-4 py-3 border-b-2 text-xs font-bold whitespace-nowrap">Pengaturan Akun</button>
                    <button @click="profileTab = 'history'" :class="profileTab === 'history' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-400'" class="px-4 py-3 border-b-2 text-xs font-bold whitespace-nowrap">Riwayat Pengaduan Saya</button>
                </div>

                <div class="p-6 sm:p-8">
                    <div x-show="profileTab === 'personal'" class="space-y-6">
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div class="bg-brand-50 p-4 rounded-2xl"><div class="text-2xl font-black text-brand-700" x-text="myReports.length"></div><div class="text-[10px] font-bold text-brand-600 uppercase mt-1">Pengaduan Diajuakan</div></div>
                            <div class="bg-emerald-50 p-4 rounded-2xl"><div class="text-2xl font-black text-emerald-700" x-text="myReports.filter(r => r.status === 'Selesai').length"></div><div class="text-[10px] font-bold text-emerald-600 uppercase mt-1">Laporan Selesai</div></div>
                            <div class="bg-red-50 p-4 rounded-2xl"><div class="text-2xl font-black text-red-700" x-text="myReports.filter(r => r.status === 'Ditolak').length"></div><div class="text-[10px] font-bold text-red-600 uppercase mt-1">Laporan Ditolak</div></div>
                        </div>
                        <form @submit.prevent="saveProfile()" class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Nama Lengkap</label>
                                <input x-model="profileForm.nama" required minlength="3" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Nomor WhatsApp</label>
                                <input x-model="profileForm.phone" required class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Alamat Domisili / RT / RW</label>
                                <input x-model="profileForm.domisili" required class="form-input">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="form-label">Pasfoto Profil</label>
                                <input type="file" accept="image/*" @change="handleProfilePhoto($event)" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-brand-50 file:text-brand-700">
                            </div>
                            <button type="submit" class="sm:col-span-2 bg-brand-600 hover:bg-brand-700 text-white py-3 rounded-xl text-xs font-bold">Simpan Profil</button>
                        </form>
                    </div>

                    <div x-show="profileTab === 'account'" class="max-w-xl">
                        <h3 class="text-base font-black text-slate-900 mb-4">Ubah Kata Sandi</h3>
                        <form @submit.prevent="changePassword()" class="space-y-4">
                            <div><label class="form-label">Kata Sandi Lama</label><input type="password" x-model="passwordForm.oldPassword" required class="form-input"></div>
                            <div><label class="form-label">Kata Sandi Baru</label><input type="password" x-model="passwordForm.newPassword" required minlength="8" class="form-input"></div>
                            <div><label class="form-label">Konfirmasi Kata Sandi Baru</label><input type="password" x-model="passwordForm.confirmPassword" required class="form-input"></div>
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-xl text-xs font-bold">Perbarui Kata Sandi</button>
                        </form>
                        <button @click="logout()" class="mt-8 w-full bg-red-50 text-red-600 hover:bg-red-100 py-3 rounded-xl text-xs font-extrabold">Keluar dari Sesi Akun</button>
                    </div>

                    <div x-show="profileTab === 'history'" class="space-y-3">
                        <template x-for="item in myReports" :key="item.id">
                            <button @click="viewDetail(item)" class="w-full text-left bg-slate-50 border border-slate-200 rounded-2xl p-4 flex justify-between items-center gap-4">
                                <div><span class="font-mono text-[10px] text-brand-600 font-bold" x-text="item.id"></span><h4 class="text-sm font-bold text-slate-900 mt-1" x-text="item.judul"></h4></div>
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full" :class="getStatusBadgeClass(item.status)" x-text="item.status"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer x-show="!(isLoggedIn && (role === 'admin' || role === 'warga')) || currentPage === 'landing'" class="bg-white border-t border-slate-200/80 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-400 font-medium">
            <div class="flex items-center space-x-2">
                <div class="w-5 h-5 bg-emerald-600 text-white rounded-lg flex items-center justify-center text-[10px] font-black">E</div>
                <span class="font-bold text-slate-700">E-Report Platform Sistem Pengaduan Digital</span>
            </div>
            <div>
                &copy; 2026 E-Report. Seluruh Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </footer>

    <!-- APP SCRIPT & REACTIVE ENGINE -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        function reportApp() {
            return {
                currentPage: 'admin-dashboard',
                isLoggedIn: true,
                role: 'admin',
                filterStatus: 'all',
                filterCategory: 'all',
                searchQuery: '',
                selectedReport: null,
                adminTanggapan: '',
                toasts: [],
                profileTab: 'personal',
                newComment: '',
                editingReportId: null,
                showOfficerForm: false,
                officerForm: { nama: '', bidang: '', status: 'Aktif' },
                officers: [
                    { id: 1, nama: 'Rizky Pratama', initials: 'RP', email: 'rizky@ereport.id', bidang: 'Infrastruktur', tugas: 2, status: 'Aktif' },
                    { id: 2, nama: 'Dewi Lestari', initials: 'DL', email: 'dewi@ereport.id', bidang: 'Kebersihan', tugas: 3, status: 'Aktif' },
                    { id: 3, nama: 'Arif Hidayat', initials: 'AH', email: 'arif@ereport.id', bidang: 'Fasilitas Publik', tugas: 1, status: 'Nonaktif' }
                ],

                init() {
                    this.$watch('currentPage', () => {
                        this.$nextTick(() => lucide.createIcons());
                    });
                },

                currentUser: {
                    nama: 'Budi Santoso',
                    nik: '3201982736450001',
                    email: 'budi@warga.id',
                    phone: '0812-3456-7890',
                    domisili: 'RT 03 / RW 04 Kelurahan Sukamaju',
                    initial: 'BS',
                    avatar: ''
                },
                profileForm: {
                    nama: 'Budi Santoso',
                    phone: '0812-3456-7890',
                    domisili: 'RT 03 / RW 04 Kelurahan Sukamaju'
                },
                passwordForm: { oldPassword: '', newPassword: '', confirmPassword: '' },
                registration: { nik: '', nama: '', email: '', phone: '', password: '', kelurahan: '', rtRw: '' },

                loginForm: { email: '', password: '' },
                newReport: { judul: '', kategori: 'Infrastruktur', rtRw: '', lokasi: '', deskripsi: '', foto: '' },

                reports: [
                    {
                        id: 'REP-2026-001',
                        pelapor: 'Budi Santoso',
                        judul: 'Jalan Utama Berlubang Parah',
                        kategori: 'Infrastruktur',
                        rtRw: 'RT 03 / RW 04',
                        lokasi: 'Jl. Raya Sukaraja No. 45',
                        deskripsi: 'Terdapat lubang berdiameter 60cm yang sangat membahayakan pengguna jalan terutama saat hujan lebat.',
                        tanggal: '01 Nov 2026',
                        status: 'Pending',
                        foto: 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?auto=format&fit=crop&w=600&q=80',
                        tanggapan: '',
                        petugasResponder: '',
                        fotoSelesai: '',
                        comments: [],
                        history: [
                            { id: 1, type: 'status', timestamp: '01 Nov 2026, 08:30 WIB', title: 'Pengaduan diterima', description: 'Laporan masuk ke antrean verifikasi.' },
                            { id: 2, type: 'status', timestamp: '01 Nov 2026, 09:15 WIB', title: 'Status: Pending', description: 'Menunggu verifikasi petugas.' }
                        ]
                    },
                    {
                        id: 'REP-2026-002',
                        pelapor: 'Siti Aminah',
                        judul: 'Penumpukan Sampah Liar',
                        kategori: 'Kebersihan',
                        rtRw: 'RT 01 / RW 02',
                        lokasi: 'Area Dekat Lapangan Warga RT 03',
                        deskripsi: 'Sampah belum diangkut armada kebersihan selama 4 hari sehingga menebar bau tidak sedap.',
                        tanggal: '29 Okt 2026',
                        status: 'Proses',
                        foto: 'https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=600&q=80',
                        tanggapan: 'Tim kebersihan DLH telah menerjunkan armada pengangkut menuju lokasi pada 30 Oktober.',
                        petugasResponder: 'Petugas DLH Wilayah II',
                        fotoSelesai: '',
                        comments: [],
                        history: [
                            { id: 1, type: 'status', timestamp: '29 Okt 2026, 07:40 WIB', title: 'Pengaduan diterima', description: 'Laporan masuk ke antrean verifikasi.' },
                            { id: 2, type: 'status', timestamp: '30 Okt 2026, 10:05 WIB', title: 'Status: Proses', description: 'Petugas DLH Wilayah II mulai menangani laporan.' }
                        ]
                    },
                    {
                        id: 'REP-2026-003',
                        pelapor: 'Ahmad Dahlan',
                        judul: 'Lampu PJU Padam Total',
                        kategori: 'Fasilitas Publik',
                        rtRw: 'RT 04 / RW 01',
                        lokasi: 'Jl. Melati Utama Blok B',
                        deskripsi: 'Dua tiang penerangan jalan mati sejak kemarin malam mengakibatkan jalanan sangat gelap.',
                        tanggal: '25 Okt 2026',
                        status: 'Selesai',
                        foto: 'https://images.unsplash.com/photo-1509114397022-ed747cca3f65?auto=format&fit=crop&w=600&q=80',
                        tanggapan: 'Petugas teknis PLN/PJU telah mengganti bohlam dan komponen trafo yang rusak.',
                        petugasResponder: 'Tim Teknisi PJU Dinas Perhubungan',
                        fotoSelesai: '',
                        comments: [],
                        history: [
                            { id: 1, type: 'status', timestamp: '25 Okt 2026, 19:20 WIB', title: 'Pengaduan diterima', description: 'Laporan masuk ke antrean verifikasi.' },
                            { id: 2, type: 'status', timestamp: '26 Okt 2026, 08:15 WIB', title: 'Status: Proses', description: 'Tim teknis melakukan pemeriksaan lokasi.' },
                            { id: 3, type: 'status', timestamp: '26 Okt 2026, 14:30 WIB', title: 'Status: Selesai', description: 'Penanganan selesai dan divalidasi petugas.' }
                        ]
                    }
                ],

                get filteredReports() {
                    return this.reports.filter(r => {
                        const matchStatus = this.filterStatus === 'all' || r.status === this.filterStatus;
                        const matchCategory = this.filterCategory === 'all' || r.kategori === this.filterCategory;
                        const matchQuery = r.judul.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           r.lokasi.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           r.pelapor.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                           r.id.toLowerCase().includes(this.searchQuery.toLowerCase());
                        return matchStatus && matchCategory && matchQuery;
                    });
                },

                get completionRate() {
                    if (this.reports.length === 0) return 0;
                    const finished = this.reports.filter(r => r.status === 'Selesai').length;
                    return Math.round((finished / this.reports.length) * 100);
                },

                get myReports() {
                    return this.reports.filter(report => report.pelapor === this.currentUser.nama);
                },

                setRole(newRole) {
                    this.role = newRole;
                    this.isLoggedIn = true;
                    this.currentPage = newRole === 'admin' ? 'admin-dashboard' : 'warga-dashboard';
                    this.showToast(`Berhasil beralih ke Mode ${newRole.toUpperCase()}`, 'info', 'refresh-cw');
                },

                updateReportStatus(report, status) {
                    report.status = status;
                    this.showToast(`Pengaduan ${report.id} diperbarui menjadi ${status}.`, 'success', 'check-circle-2');
                },

                addOfficer() {
                    if (!this.officerForm.nama.trim() || !this.officerForm.bidang.trim()) {
                        this.showToast('Lengkapi nama dan bidang petugas.', 'warning', 'triangle-alert');
                        return;
                    }
                    const initials = this.officerForm.nama.split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase();
                    this.officers.push({
                        id: Date.now(),
                        nama: this.officerForm.nama.trim(),
                        initials,
                        email: `${this.officerForm.nama.toLowerCase().replace(/\s+/g, '.')}@ereport.id`,
                        bidang: this.officerForm.bidang.trim(),
                        tugas: 0,
                        status: this.officerForm.status
                    });
                    this.officerForm = { nama: '', bidang: '', status: 'Aktif' };
                    this.showOfficerForm = false;
                    this.showToast('Petugas baru berhasil ditambahkan.', 'success', 'user-plus');
                },

                assignOfficer(officer, reportId) {
                    if (!reportId) return;
                    const report = this.reports.find(item => item.id === reportId);
                    if (report) {
                        report.status = report.status === 'Pending' ? 'Proses' : report.status;
                        report.petugasResponder = officer.nama;
                        officer.tugas += 1;
                        this.showToast(`Laporan ${reportId} ditugaskan kepada ${officer.nama}.`, 'success', 'clipboard-check');
                    }
                },

                login() {
                    this.isLoggedIn = true;
                    this.currentPage = this.role === 'admin' ? 'admin-dashboard' : 'warga-dashboard';
                    this.showToast('Login berhasil! Selamat datang kembali.', 'success', 'check-circle-2');
                },

                logout() {
                    this.isLoggedIn = false;
                    this.currentPage = 'landing';
                    this.showToast('Sesi akun telah selesai.', 'warning', 'log-out');
                },

                handleFileUpload(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.newReport.foto = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                submitReport() {
                    if (this.editingReportId) {
                        const report = this.reports.find(item => item.id === this.editingReportId);
                        if (report && report.status === 'Pending') {
                            Object.assign(report, {
                                judul: this.newReport.judul,
                                kategori: this.newReport.kategori,
                                rtRw: this.newReport.rtRw,
                                lokasi: this.newReport.lokasi,
                                deskripsi: this.newReport.deskripsi,
                                foto: this.newReport.foto || report.foto
                            });
                            this.editingReportId = null;
                            this.newReport = { judul: '', kategori: 'Infrastruktur', rtRw: '', lokasi: '', deskripsi: '', foto: '' };
                            this.currentPage = 'warga-dashboard';
                            this.showToast('Pengaduan berhasil diperbarui.', 'success', 'check-circle-2');
                            return;
                        }
                    }
                    const nextNum = String(this.reports.length + 1).padStart(3, '0');
                    const newId = `REP-2026-${nextNum}`;
                    this.reports.unshift({
                        id: newId,
                        pelapor: this.currentUser.nama,
                        judul: this.newReport.judul,
                        kategori: this.newReport.kategori,
                        rtRw: this.newReport.rtRw || 'RT 01 / RW 01',
                        lokasi: this.newReport.lokasi,
                        deskripsi: this.newReport.deskripsi,
                        tanggal: '02 Nov 2026',
                        status: 'Pending',
                        foto: this.newReport.foto || 'https://images.unsplash.com/photo-1584467735815-f778f274e296?auto=format&fit=crop&w=600&q=80',
                        tanggapan: '',
                        petugasResponder: '',
                        fotoSelesai: '',
                        comments: [],
                        history: [{ id: Date.now(), type: 'status', timestamp: new Date().toLocaleString('id-ID'), title: 'Pengaduan diterima', description: 'Laporan masuk ke antrean verifikasi.' }]
                    });

                    this.newReport = { judul: '', kategori: 'Infrastruktur', rtRw: '', lokasi: '', deskripsi: '', foto: '' };
                    this.currentPage = 'warga-dashboard';
                    this.showToast('Pengaduan baru berhasil dikirimkan!', 'success', 'send');
                },

                viewDetail(report) {
                    this.selectedReport = report;
                    this.adminTanggapan = report.tanggapan || '';
                    this.currentPage = this.role === 'admin'
                        ? 'admin-detail-pengaduan'
                        : 'warga-detail-pengaduan';
                    this.$nextTick(() => {
                        lucide.createIcons();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                },

                navigateToProfile() {
                    this.currentPage = this.role === 'admin' ? 'admin-profil' : 'warga-profil';
                    this.profileTab = 'personal';
                    this.$nextTick(() => lucide.createIcons());
                },

                updateStatus(newStatus) {
                    if (this.selectedReport) {
                        this.selectedReport.status = newStatus;
                        this.selectedReport.history.push({
                            id: Date.now(),
                            type: 'status',
                            timestamp: new Date().toLocaleString('id-ID'),
                            title: `Status: ${newStatus}`,
                            description: `Status laporan diperbarui oleh Petugas Admin.`
                        });
                        this.showToast(`Status laporan diubah ke ${newStatus}`, 'info', 'sliders');
                    }
                },

                saveTanggapan() {
                    if (this.selectedReport) {
                        this.selectedReport.tanggapan = this.adminTanggapan;
                        this.selectedReport.petugasResponder = 'Petugas Admin Wilayah';
                        this.showToast('Tanggapan resmi petugas berhasil disimpan!', 'success', 'check-check');
                    }
                },

                addComment() {
                    const message = this.newComment.trim();
                    if (!this.selectedReport || !message) return;
                    this.selectedReport.comments.push({
                        id: Date.now(),
                        author: this.currentUser.nama,
                        message,
                        timestamp: new Date().toLocaleString('id-ID')
                    });
                    this.selectedReport.history.push({
                        id: Date.now() + 1,
                        type: 'comment',
                        timestamp: new Date().toLocaleString('id-ID'),
                        title: 'Pembaruan dari warga',
                        description: message
                    });
                    this.newComment = '';
                    this.$nextTick(() => lucide.createIcons());
                },

                handleCompletionPhoto(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (!file.type.startsWith('image/')) {
                        this.showToast('File bukti harus berupa gambar.', 'error', 'circle-alert');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.selectedReport.fotoSelesai = e.target.result;
                        this.selectedReport.history.push({
                            id: Date.now(),
                            type: 'status',
                            timestamp: new Date().toLocaleString('id-ID'),
                            title: 'Bukti penanganan diunggah',
                            description: 'Petugas melampirkan foto hasil penanganan.'
                        });
                        this.showToast('Foto bukti penanganan berhasil diunggah.', 'success', 'image');
                    };
                    reader.readAsDataURL(file);
                },

                printReport() {
                    if (!this.selectedReport) return;
                    window.print();
                },

                editReport(report) {
                    if (report.status !== 'Pending') {
                        this.showToast('Hanya laporan berstatus Pending yang dapat diedit.', 'warning', 'circle-alert');
                        return;
                    }
                    this.editingReportId = report.id;
                    this.newReport = {
                        judul: report.judul,
                        kategori: report.kategori,
                        rtRw: report.rtRw,
                        lokasi: report.lokasi,
                        deskripsi: report.deskripsi,
                        foto: report.foto
                    };
                    this.currentPage = 'warga-buat';
                },

                deleteReport(report) {
                    if (report.status !== 'Pending' || report.pelapor !== this.currentUser.nama) return;
                    if (!window.confirm(`Hapus pengaduan "${report.judul}"?`)) return;
                    this.reports = this.reports.filter(item => item.id !== report.id);
                    this.showToast('Pengaduan berhasil dihapus.', 'success', 'trash-2');
                },

                exportReportsCsv() {
                    const headers = ['ID', 'Pelapor', 'Judul', 'Kategori', 'RT/RW', 'Lokasi', 'Tanggal', 'Status', 'Tanggapan'];
                    const rows = this.reports.map(report => [
                        report.id, report.pelapor, report.judul, report.kategori, report.rtRw,
                        report.lokasi, report.tanggal, report.status, report.tanggapan || ''
                    ]);
                    const escapeCsv = value => `"${String(value ?? '').replace(/"/g, '""')}"`;
                    const csv = [headers, ...rows].map(row => row.map(escapeCsv).join(',')).join('\n');
                    const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `rekap-pengaduan-${new Date().toISOString().slice(0, 10)}.csv`;
                    link.click();
                    URL.revokeObjectURL(url);
                    this.showToast('Rekap pengaduan berhasil diekspor.', 'success', 'download');
                },

                registerCitizen() {
                    if (!/^\d{16}$/.test(this.registration.nik)) {
                        this.showToast('NIK harus terdiri dari tepat 16 digit.', 'error', 'circle-alert');
                        return;
                    }
                    if (!this.registration.nama.trim() || this.registration.password.length < 8) {
                        this.showToast('Nama dan kata sandi belum memenuhi ketentuan.', 'error', 'circle-alert');
                        return;
                    }
                    this.currentUser = {
                        nama: this.registration.nama,
                        nik: this.registration.nik,
                        email: this.registration.email,
                        phone: this.registration.phone,
                        domisili: `${this.registration.rtRw} Kelurahan ${this.registration.kelurahan}`,
                        initial: this.registration.nama.split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase(),
                        avatar: ''
                    };
                    this.profileForm = { nama: this.currentUser.nama, phone: this.currentUser.phone, domisili: this.currentUser.domisili };
                    this.registration = { nik: '', nama: '', email: '', phone: '', password: '', kelurahan: '', rtRw: '' };
                    this.isLoggedIn = true;
                    this.role = 'warga';
                    this.currentPage = 'warga-dashboard';
                    this.showToast('Registrasi warga berhasil. Selamat datang!', 'success', 'user-check');
                },

                handleProfilePhoto(event) {
                    const file = event.target.files[0];
                    if (!file || !file.type.startsWith('image/')) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.currentUser.avatar = e.target.result;
                        this.showToast('Pasfoto profil siap disimpan.', 'info', 'image');
                    };
                    reader.readAsDataURL(file);
                },

                saveProfile() {
                    if (!this.profileForm.nama.trim() || !this.profileForm.phone.trim() || !this.profileForm.domisili.trim()) {
                        this.showToast('Semua data profil wajib diisi.', 'error', 'circle-alert');
                        return;
                    }
                    this.currentUser.nama = this.profileForm.nama.trim();
                    this.currentUser.phone = this.profileForm.phone.trim();
                    this.currentUser.domisili = this.profileForm.domisili.trim();
                    this.currentUser.initial = this.currentUser.nama.split(' ').map(word => word[0]).join('').slice(0, 2).toUpperCase();
                    this.showToast('Data profil berhasil diperbarui.', 'success', 'check-circle-2');
                },

                changePassword() {
                    if (this.passwordForm.oldPassword !== 'password') {
                        this.showToast('Kata sandi lama tidak sesuai.', 'error', 'key-round');
                        return;
                    }
                    if (this.passwordForm.newPassword.length < 8) {
                        this.showToast('Kata sandi baru minimal 8 karakter.', 'error', 'key-round');
                        return;
                    }
                    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
                        this.showToast('Konfirmasi kata sandi baru tidak cocok.', 'error', 'key-round');
                        return;
                    }
                    this.passwordForm = { oldPassword: '', newPassword: '', confirmPassword: '' };
                    this.showToast('Kata sandi berhasil diperbarui.', 'success', 'shield-check');
                },

                showToast(message, type = 'info', icon = 'info') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type, icon });
                    setTimeout(() => this.removeToast(id), 3500);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                getStatusBadgeClass(status) {
                    switch (status) {
                        case 'Pending': return 'bg-amber-50 text-amber-700 border border-amber-200/80 font-bold';
                        case 'Proses': return 'bg-teal-50 text-teal-800 border border-teal-200/80 font-bold';
                        case 'Selesai': return 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold';
                        case 'Ditolak': return 'bg-rose-50 text-rose-700 border border-rose-200/80 font-bold';
                        default: return 'bg-slate-50 text-slate-700 border border-slate-200 font-bold';
                    }
                }
            }
        }
    </script>
</body>
</html>
