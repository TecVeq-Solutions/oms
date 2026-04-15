<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ app_setting('site_name', config('app.name', 'Office Management System')) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(46, 63, 122, 0.16), transparent 30%),
                radial-gradient(circle at bottom right, rgba(11, 21, 51, 0.18), transparent 28%),
                linear-gradient(180deg, #f8faff 0%, #eef3ff 100%);
        }

        .brand-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
        }

        .brand-border {
            border-color: rgba(46, 63, 122, 0.14);
        }

        .brand-shadow {
            box-shadow: 0 20px 60px rgba(11, 21, 51, 0.10);
        }

        .brand-text {
            color: #0B1533;
        }

        .brand-subtext {
            color: #5C6784;
        }

        .brand-accent {
            color: #2E3F7A;
        }

        .brand-bg {
            background: linear-gradient(135deg, #0B1533 0%, #1B2A57 55%, #2E3F7A 100%);
        }

        .brand-soft {
            background: linear-gradient(135deg, rgba(201, 212, 255, 0.50), rgba(255, 255, 255, 0.9));
        }

        .hero-pattern {
            background-image:
                linear-gradient(rgba(46, 63, 122, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(46, 63, 122, 0.06) 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="antialiased text-slate-800">
    <div class="min-h-screen">
        @if (Route::has('login'))
            <header class="w-full">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-6">
                    <div class="flex items-center justify-between rounded-2xl border brand-border brand-card brand-shadow px-4 py-3 sm:px-6">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/tecveq.png') }}" alt="TECVEQ" class="h-10 w-auto">
                            <div>
                                <p class="text-sm font-semibold tracking-[0.2em] uppercase brand-accent">TECVEQ</p>
                                <p class="text-sm brand-subtext">Office Management System</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="inline-flex items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white brand-bg shadow-lg shadow-blue-900/20 transition hover:opacity-95">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center rounded-xl border px-5 py-2.5 text-sm font-semibold brand-text brand-border bg-white hover:bg-slate-50 transition">
                                    Log In
                                </a>

                                {{-- @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white brand-bg shadow-lg shadow-blue-900/20 transition hover:opacity-95">
                                        Get Started
                                    </a>
                                @endif --}}
                            @endauth
                        </div>
                    </div>
                </div>
            </header>
        @endif

        <main class="max-w-7xl mx-auto px-6 lg:px-8 py-10 lg:py-14">
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 rounded-full border brand-border bg-white/80 px-4 py-2 text-sm font-medium brand-accent">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#2E3F7A]"></span>
                        Smart workplace operations, HR, CRM, and reporting in one platform
                    </div>

                    <div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight brand-text">
                            Manage your office operations with clarity, speed, and control.
                        </h1>

                        <p class="mt-5 max-w-2xl text-lg leading-8 brand-subtext">
                            A complete Office Management System to handle employees, attendance,
                            shifts, leave, CRM, self service actions, reporting, notifications,
                            and access security from one unified dashboard.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center justify-center rounded-2xl px-6 py-3.5 text-base font-semibold text-white brand-bg shadow-xl shadow-blue-950/20 transition hover:scale-[1.01]">
                                Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center rounded-2xl px-6 py-3.5 text-base font-semibold text-white brand-bg shadow-xl shadow-blue-950/20 transition hover:scale-[1.01]">
                                Log In to System
                            </a>

                            {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center justify-center rounded-2xl border brand-border bg-white px-6 py-3.5 text-base font-semibold brand-text transition hover:bg-slate-50">
                                    Create Account
                                </a>
                            @endif --}}
                        @endauth
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div class="rounded-2xl brand-card border brand-border p-4 brand-shadow">
                            <p class="text-2xl font-extrabold brand-text">20+</p>
                            <p class="mt-1 text-sm brand-subtext">Core Modules</p>
                        </div>
                        <div class="rounded-2xl brand-card border brand-border p-4 brand-shadow">
                            <p class="text-2xl font-extrabold brand-text">HR</p>
                            <p class="mt-1 text-sm brand-subtext">Office & staff workflows</p>
                        </div>
                        <div class="rounded-2xl brand-card border brand-border p-4 brand-shadow">
                            <p class="text-2xl font-extrabold brand-text">CRM</p>
                            <p class="mt-1 text-sm brand-subtext">Lead and campaign tracking</p>
                        </div>
                        <div class="rounded-2xl brand-card border brand-border p-4 brand-shadow">
                            <p class="text-2xl font-extrabold brand-text">Reports</p>
                            <p class="mt-1 text-sm brand-subtext">Operational visibility</p>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-3 rounded-[2rem] bg-gradient-to-br from-[#c9d4ff]/60 to-white/20 blur-2xl"></div>

                    <div class="relative overflow-hidden rounded-[2rem] border brand-border brand-card brand-shadow">
                        <div class="hero-pattern p-6 lg:p-8">
                            <div class="flex items-center justify-between gap-4 rounded-2xl bg-white/85 p-4 border brand-border">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-2xl brand-bg flex items-center justify-center shadow-md">
                                        <img src="{{ asset('images/tecveq.png') }}" alt="TECVEQ" class="h-7 w-auto">
                                    </div>
                                    <div>
                                        <h2 class="text-lg font-bold brand-text">Office Management System</h2>
                                        <p class="text-sm brand-subtext">Secure. Organized. Scalable.</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                    Active
                                </span>
                            </div>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="rounded-2xl bg-white p-5 border brand-border">
                                    <p class="text-sm font-semibold brand-accent">Today Overview</p>
                                    <h3 class="mt-3 text-3xl font-bold brand-text">Attendance</h3>
                                    <p class="mt-2 text-sm brand-subtext">Track check-in, check-out, leave, and shift activity.</p>
                                </div>

                                <div class="rounded-2xl bg-white p-5 border brand-border">
                                    <p class="text-sm font-semibold brand-accent">Security Control</p>
                                    <h3 class="mt-3 text-3xl font-bold brand-text">Allowed IPs</h3>
                                    <p class="mt-2 text-sm brand-subtext">Protect access with approved network locations.</p>
                                </div>

                                <div class="rounded-2xl bg-white p-5 border brand-border sm:col-span-2">
                                    <p class="text-sm font-semibold brand-accent">Business Modules</p>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach ([
                                            'Dashboard',
                                            'User Roles',
                                            'Shifts',
                                            'Allowed IPs',
                                            'Notifications',
                                            'Employees',
                                            'Attendance',
                                            'Leave Requests',
                                            'Leave Types',
                                            'Settings',
                                            'Leads',
                                            'My Leads',
                                            'Email Campaigns',
                                            'AI History',
                                            'Mark Attendance',
                                            'Check-Out',
                                            'My Leaves',
                                            'Leave Balance',
                                            'Attendance Reports',
                                            'Lead Reports',
                                            'Campaign Reports',
                                            'Leave Reports',
                                        ] as $feature)
                                            <span class="rounded-full px-3 py-2 text-sm font-medium text-[#1B2A57] brand-soft border brand-border">
                                                {{ $feature }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="rounded-2xl bg-[#0B1533] p-5 text-white">
                                    <p class="text-sm text-blue-100">Office & HR</p>
                                    <ul class="mt-3 space-y-2 text-sm text-blue-50">
                                        <li>Employees</li>
                                        <li>Attendance</li>
                                        <li>Leave Requests</li>
                                        <li>Leave Types</li>
                                        <li>Settings</li>
                                    </ul>
                                </div>

                                <div class="rounded-2xl bg-[#1B2A57] p-5 text-white">
                                    <p class="text-sm text-blue-100">CRM & Marketing</p>
                                    <ul class="mt-3 space-y-2 text-sm text-blue-50">
                                        <li>Leads</li>
                                        <li>My Leads</li>
                                        <li>Email Campaigns</li>
                                        <li>AI History</li>
                                    </ul>
                                </div>

                                <div class="rounded-2xl bg-[#2E3F7A] p-5 text-white">
                                    <p class="text-sm text-blue-100">Reports & Self Service</p>
                                    <ul class="mt-3 space-y-2 text-sm text-blue-50">
                                        <li>Mark Attendance</li>
                                        <li>Check-Out</li>
                                        <li>My Leaves</li>
                                        <li>Leave Balance</li>
                                        <li>Reports</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-12 lg:mt-16">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    <div class="rounded-3xl border brand-border brand-card p-6 brand-shadow">
                        <div class="h-12 w-12 rounded-2xl bg-[#E8EEFF] flex items-center justify-center text-[#2E3F7A] font-bold">
                            01
                        </div>
                        <h3 class="mt-4 text-xl font-bold brand-text">Administration</h3>
                        <p class="mt-3 text-sm leading-6 brand-subtext">
                            Manage dashboard access, user roles, shifts, system notifications,
                            and IP restrictions from one central workspace.
                        </p>
                    </div>

                    <div class="rounded-3xl border brand-border brand-card p-6 brand-shadow">
                        <div class="h-12 w-12 rounded-2xl bg-[#E8EEFF] flex items-center justify-center text-[#2E3F7A] font-bold">
                            02
                        </div>
                        <h3 class="mt-4 text-xl font-bold brand-text">Office & HR</h3>
                        <p class="mt-3 text-sm leading-6 brand-subtext">
                            Handle employees, attendance, leave requests, leave types,
                            balances, and operational settings efficiently.
                        </p>
                    </div>

                    <div class="rounded-3xl border brand-border brand-card p-6 brand-shadow">
                        <div class="h-12 w-12 rounded-2xl bg-[#E8EEFF] flex items-center justify-center text-[#2E3F7A] font-bold">
                            03
                        </div>
                        <h3 class="mt-4 text-xl font-bold brand-text">CRM & Outreach</h3>
                        <p class="mt-3 text-sm leading-6 brand-subtext">
                            Track leads, monitor personal pipelines, run email campaigns,
                            and review AI-assisted history for better follow-up.
                        </p>
                    </div>

                    <div class="rounded-3xl border brand-border brand-card p-6 brand-shadow">
                        <div class="h-12 w-12 rounded-2xl bg-[#E8EEFF] flex items-center justify-center text-[#2E3F7A] font-bold">
                            04
                        </div>
                        <h3 class="mt-4 text-xl font-bold brand-text">Reports & Self Service</h3>
                        <p class="mt-3 text-sm leading-6 brand-subtext">
                            Empower employees with mark attendance, check-out, leave access,
                            and detailed reporting for teams and management.
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-12 lg:mt-16">
                <div class="rounded-[2rem] overflow-hidden border brand-border brand-shadow">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="p-8 lg:p-10 bg-white">
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] brand-accent">Why this platform</p>
                            <h2 class="mt-3 text-3xl font-bold brand-text">
                                Built for daily office operations and long-term scalability
                            </h2>
                            <p class="mt-4 text-base leading-7 brand-subtext">
                                This welcome page is designed to reflect your full system rather than the default
                                Laravel starter page. It presents your brand, your modules, and your product value
                                clearly from the first screen.
                            </p>

                            <div class="mt-6 space-y-4">
                                <div class="flex gap-3">
                                    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-[#2E3F7A]"></div>
                                    <p class="text-sm brand-subtext">Clear TECVEQ branding with logo-first identity</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-[#2E3F7A]"></div>
                                    <p class="text-sm brand-subtext">Office, HR, CRM, self service, and reports all represented</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="mt-1 h-2.5 w-2.5 rounded-full bg-[#2E3F7A]"></div>
                                    <p class="text-sm brand-subtext">Professional dark blue palette based on your logo scheme</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 lg:p-10 text-white brand-bg">
                            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-blue-100">Quick access</p>
                            <h2 class="mt-3 text-3xl font-bold">
                                Ready to enter the system?
                            </h2>
                            <p class="mt-4 text-blue-100 leading-7">
                                Access your dashboard, manage employees, handle lead workflows,
                                monitor attendance, and generate reports with a modern interface.
                            </p>

                            <div class="mt-8 flex flex-wrap gap-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-[#0B1533] hover:bg-slate-100 transition">
                                        Open Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-[#0B1533] hover:bg-slate-100 transition">
                                        Login
                                    </a>

                                    {{-- @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                           class="inline-flex items-center justify-center rounded-2xl border border-white/30 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10 transition">
                                            Register
                                        </a>
                                    @endif --}}
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="mt-12 pb-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-sm brand-subtext">
                    <p>© {{ date('Y') }} {{ app_setting('site_name', config('app.name', 'Office Management System')) }}. All rights reserved.</p>
                    <p>Powered by TECVEQ branding</p>
                </div>
            </footer>
        </main>
    </div>
</body>
</html>