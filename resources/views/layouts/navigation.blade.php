<aside x-data="{ open: false }"
    class="fixed inset-y-0 left-0 z-40 w-72 hidden lg:flex lg:flex-col text-white overflow-hidden"
    style="background: linear-gradient(180deg, #081127 0%, #0B1533 22%, #13214a 58%, #1B2A57 100%); box-shadow: 10px 0 35px rgba(11,21,51,.18);">

    @php
        $siteName = app_setting('site_name', config('app.name', 'Office Suite'));
        $siteTagline = app_setting('site_tagline', 'Manage employees, attendance, CRM and campaigns');

        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
        $isEmployee = $user && method_exists($user, 'hasRole') ? $user->hasRole('employee') : false;
    @endphp

    <div class="absolute inset-0 pointer-events-none opacity-20" style="background-image:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,.16), transparent 22%),
            radial-gradient(circle at 80% 10%, rgba(201,212,255,.16), transparent 18%),
            linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: auto, auto, 26px 26px, 26px 26px;">
    </div>

    <!-- Brand -->
    <div class="relative h-20 px-6 border-b border-white/10 flex items-center gap-4 backdrop-blur-sm">
        <a href="{{ route('dashboard') }}" class="shrink-0">
            <div
                class="h-11 w-11 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center shadow-lg">
                <img src="{{ asset('images/tecveq.png') }}" alt="TECVEQ" class="block h-8 w-auto" />
            </div>
        </a>

        <div class="min-w-0">
            <h2 class="text-sm font-bold text-white truncate">{{ $siteName }}</h2>
            <p class="text-xs text-blue-200/90 truncate">{{ $siteTagline }}</p>
        </div>
    </div>

    @php
        $linkBase = 'group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-[13px] font-medium transition-all duration-200';
        $activeClass = 'bg-white/14 text-white shadow-inner border border-white/10';
        $inactiveClass = 'text-blue-100/90 hover:bg-white/8 hover:text-white';
        $badgeClass = 'ml-auto inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 rounded-full text-[10px] font-bold bg-white/18 text-white';
    @endphp

    <!-- Nav -->
    <div class="relative flex-1 overflow-y-auto px-4 py-5 space-y-6">

        <!-- Main -->
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">Main</p>
            <div class="space-y-1.5">
                @can('view dashboard')
                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🏠</span>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                @endcan

                @if($isAdmin)
                    <a href="{{ route('user-roles.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('user-roles.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🛡️</span>
                        <span>{{ __('User Roles') }}</span>
                    </a>

                    <a href="{{ route('shifts.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('shifts.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🕒</span>
                        <span>{{ __('Shifts') }}</span>
                    </a>

                    <a href="{{ route('allowed-ips.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('allowed-ips.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🌐</span>
                        <span>{{ __('Allowed IPs') }}</span>
                    </a>
                @endif

                @can('view notifications')
                    <a href="{{ route('notifications.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('notifications.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🔔</span>
                        <span>{{ __('Notifications') }}</span>

                        @if(($globalUnreadNotificationCount ?? 0) > 0)
                            <span class="{{ $badgeClass }}">
                                {{ ($globalUnreadNotificationCount ?? 0) > 99 ? '99+' : $globalUnreadNotificationCount }}
                            </span>
                        @endif
                    </a>
                @endcan
            </div>
        </div>
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">Task System</p>
            <div class="space-y-1.5">

                @can('view workspaces')
                    <a href="{{ route('workspaces.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('workspaces.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">🗂️</span>
                        <span>{{ __('Workspaces') }}</span>
                    </a>
                @endcan

                @can('view projects')
                    <a href="{{ route('projects.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('projects.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">📁</span>
                        <span>{{ __('Projects') }}</span>
                    </a>
                @endcan

                @can('view tasks')
                    <a href="{{ route('tasks.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('tasks.*') && !request()->routeIs('tasks.my') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">✅</span>
                        <span>{{ __('Tasks') }}</span>
                    </a>
                @endcan

                @can('view own tasks')
                    <a href="{{ route('tasks.my') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('tasks.my') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">📌</span>
                        <span>{{ __('My Tasks') }}</span>
                    </a>
                @endcan
                @can('approve task extension')
                    <a href="{{ route('task-extension-requests.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('task-extension-requests.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">⏳</span>
                        <span>{{ __('Extension Requests') }}</span>
                    </a>
                @endcan
                @can('view task reports')
                    <a href="{{ route('task-reports.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('task-reports.index') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">📊</span>
                        <span>{{ __('Task Reports') }}</span>
                    </a>
                @endcan

                <a href="{{ route('task-reports.my') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('task-reports.my') ? $activeClass : $inactiveClass }}">
                    <span class="text-base">🧾</span>
                    <span>{{ __('My Task Report') }}</span>
                </a>

            </div>
        </div>


        <!-- Office / HR -->
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">Office & HR</p>
            <div class="space-y-1.5">
                @can('view employees')
                    <a href="{{ route('employees.index') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('employees.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">👥</span>
                        <span>{{ __('Employees') }}</span>
                    </a>
                @endcan

                @can('view attendance')
                    @if(feature_enabled('attendance_module_enabled'))
                        <a href="{{ route('attendances.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('attendances.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📅</span>
                            <span>{{ __('Attendance') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view leave requests')
                    @if(feature_enabled('leave_module_enabled'))
                        <a href="{{ route('leave-requests.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('leave-requests.index') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📝</span>
                            <span>{{ __('Leave Requests') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view leave types')
                    @if(feature_enabled('leave_module_enabled'))
                        <a href="{{ route('leave-types.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('leave-types.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📂</span>
                            <span>{{ __('Leave Types') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view office settings')
                    <a href="{{ route('office-settings.edit') }}"
                        class="{{ $linkBase }} {{ request()->routeIs('office-settings.*') ? $activeClass : $inactiveClass }}">
                        <span class="text-base">⚙️</span>
                        <span>{{ __('Settings') }}</span>
                    </a>
                @endcan

                {{-- Admin/HR-only quick access for payroll module --}}
                @if($isAdmin)
                    @can('view employees')
                        <a href="{{ route('employees.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('employees.bank-account.*') || request()->routeIs('employees.salary-payments.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">🏦</span>
                            <span>{{ __('Employee Bank & Salary') }}</span>
                        </a>
                    @endcan
                @endif
            </div>
        </div>

        <!-- CRM / Campaigns -->
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">CRM & Marketing
            </p>
            <div class="space-y-1.5">
                @can('view leads')
                    @if(feature_enabled('lead_module_enabled'))
                        <a href="{{ route('leads.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('leads.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">🎯</span>
                            <span>{{ __('Leads') }}</span>
                        </a>
                    @endif
                @endcan

                @if(!$isAdmin)
                    @can('view own leads')
                        @if(feature_enabled('lead_module_enabled'))
                            <a href="{{ route('leads.my') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('leads.my') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">📌</span>
                                <span>{{ __('My Leads') }}</span>
                            </a>
                        @endif
                    @endcan
                @endif

                @can('view campaigns')
                    @if(feature_enabled('campaign_module_enabled'))
                        <a href="{{ route('email-campaigns.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('email-campaigns.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📣</span>
                            <span>{{ __('Email Campaigns') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view ai history')
                    @if(feature_enabled('ai_module_enabled'))
                        <a href="{{ route('ai-generations.index') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('ai-generations.*') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">🤖</span>
                            <span>{{ __('AI History') }}</span>
                        </a>
                    @endif
                @endcan
            </div>
        </div>

        <!-- Self Service -->
        @if(!$isAdmin)
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">Self Service</p>
                <div class="space-y-1.5">
                    @if($isEmployee)
                        <a href="{{ route('profile.employee') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('profile.employee') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">🙍</span>
                            <span>{{ __('My Profile') }}</span>
                        </a>

                        @if(feature_enabled('attendance_module_enabled'))
                            <a href="{{ route('profile.attendance') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('profile.attendance') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">📊</span>
                                <span>{{ __('My Attendance') }}</span>
                            </a>
                        @endif
                    @endif

                    @can('mark self attendance')
                        @if(feature_enabled('attendance_module_enabled'))
                            <a href="{{ route('profile.checkin.form') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('profile.checkin.*') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">✅</span>
                                <span>{{ __('Mark Attendance') }}</span>
                            </a>
                        @endif
                    @endcan

                    @can('mark self checkout')
                        @if(feature_enabled('attendance_module_enabled'))
                            <a href="{{ route('profile.checkout.form') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('profile.checkout.*') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">🚪</span>
                                <span>{{ __('Check-Out') }}</span>
                            </a>
                        @endif
                    @endcan

                    @can('apply leave')
                        @if(feature_enabled('leave_module_enabled'))
                            <a href="{{ route('leave-requests.my') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('leave-requests.my') || request()->routeIs('leave-requests.create') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">🌴</span>
                                <span>{{ __('My Leaves') }}</span>
                            </a>
                        @endif
                    @endcan

                    @can('view own leave balance')
                        @if(feature_enabled('leave_module_enabled'))
                            <a href="{{ route('leave-requests.balance') }}"
                                class="{{ $linkBase }} {{ request()->routeIs('leave-requests.balance') ? $activeClass : $inactiveClass }}">
                                <span class="text-base">💼</span>
                                <span>{{ __('Leave Balance') }}</span>
                            </a>
                        @endif
                    @endcan

                    @can('view own bank account')
                        <a href="{{ route('bank-account.my') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('bank-account.my') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">🏦</span>
                            <span>{{ __('Bank Account') }}</span>
                        </a>
                    @endcan

                    @can('view own salary payments')
                        <a href="{{ route('salary-payments.my') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('salary-payments.my') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">💰</span>
                            <span>{{ __('Salary History') }}</span>
                        </a>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Reports -->
        <div>
            <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-200/60 mb-2">Reports</p>
            <div class="space-y-1.5">
                @can('view attendance reports')
                    @if(feature_enabled('attendance_module_enabled'))
                        <a href="{{ route('reports.attendance') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('reports.attendance') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📈</span>
                            <span>{{ __('Attendance Reports') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view lead reports')
                    @if(feature_enabled('lead_module_enabled'))
                        <a href="{{ route('reports.leads') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('reports.leads') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📊</span>
                            <span>{{ __('Lead Reports') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view campaign reports')
                    @if(feature_enabled('campaign_module_enabled'))
                        <a href="{{ route('reports.email-campaigns') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('reports.email-campaigns') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📨</span>
                            <span>{{ __('Campaign Reports') }}</span>
                        </a>
                    @endif
                @endcan

                @can('view leave reports')
                    @if(feature_enabled('leave_module_enabled'))
                        <a href="{{ route('reports.leaves') }}"
                            class="{{ $linkBase }} {{ request()->routeIs('reports.leaves') ? $activeClass : $inactiveClass }}">
                            <span class="text-base">📋</span>
                            <span>{{ __('Leave Reports') }}</span>
                        </a>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</aside>