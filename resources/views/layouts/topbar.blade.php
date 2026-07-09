<div class="sticky top-0 z-30 px-4 sm:px-6 lg:px-8 pt-4">
    @php
        $recentNotifications = $globalRecentNotifications ?? collect();
        $unreadCount = $globalUnreadNotificationCount ?? 0;
    @endphp

    <div class="max-w-7xl mx-auto">
        <div class="rounded-3xl border border-[rgba(46,63,122,.10)] bg-white/80 backdrop-blur-xl shadow-[0_16px_40px_rgba(11,21,51,.06)]">
            <div class="h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-lg md:text-xl font-bold text-[#0B1533] truncate">
                        {{ app_setting('site_name', config('app.name', 'Office Suite')) }}
                    </h1>
                    <p class="text-sm text-slate-500 truncate">
                        {{ app_setting('site_tagline', 'Manage employees, attendance, CRM and campaigns') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    @can('view notifications')
                        <div class="relative" x-data="{ openNotifications: false }">
                            <button
                                @click="openNotifications = !openNotifications"
                                class="relative inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-[#EEF3FF] hover:bg-[#E3EBFF] text-[#2E3F7A] shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0018 9.75v-.7V9a6 6 0 10-12 0v.05-.05.7a8.967 8.967 0 00-2.311 6.022 23.848 23.848 0 005.454 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>

                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 text-[10px] font-bold leading-none text-white bg-[#2E3F7A] rounded-full shadow">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div
                                x-show="openNotifications"
                                @click.away="openNotifications = false"
                                x-transition
                                class="absolute right-0 mt-3 w-96 bg-white border border-[#E5ECFF] rounded-3xl shadow-2xl z-50 overflow-hidden"
                                x-cloak
                            >
                                <div class="px-5 py-4 border-b border-[#EEF3FF] flex items-center justify-between bg-[#F8FAFF]">
                                    <h3 class="text-sm font-semibold text-[#0B1533]">Notifications</h3>
                                    @if($unreadCount > 0)
                                        <span class="text-xs font-semibold text-[#2E3F7A]">{{ $unreadCount }} unread</span>
                                    @endif
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    @forelse($recentNotifications as $notification)
                                        <div class="border-b border-[#F0F4FF] last:border-b-0 {{ $notification->read_at ? 'bg-white' : 'bg-[#F6F9FF]' }}">
                                            <div class="p-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <a href="{{ route('notifications.show', $notification) }}" class="flex-1 block">
                                                        <p class="text-sm font-semibold text-[#0B1533]">{{ $notification->title }}</p>
                                                        <p class="text-xs text-slate-600 mt-1">
                                                            {{ \Illuminate\Support\Str::limit($notification->message ?? '-', 90) }}
                                                        </p>
                                                        <p class="text-[11px] text-slate-400 mt-2">
                                                            {{ $notification->created_at->format('Y-m-d H:i') }}
                                                        </p>
                                                    </a>

                                                    @if(!$notification->read_at)
                                                        <form method="POST" action="{{ route('notifications.quick-read', $notification) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                    class="text-xs px-2.5 py-1.5 rounded-xl bg-[#EAF0FF] text-[#2E3F7A] hover:bg-[#DDE7FF] transition">
                                                                Read
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-6 text-sm text-slate-500 text-center">
                                            No notifications found.
                                        </div>
                                    @endforelse
                                </div>

                                <div class="px-5 py-4 bg-[#F8FAFF] flex items-center justify-between">
                                    <a href="{{ route('notifications.index') }}"
                                       class="text-sm font-semibold text-[#2E3F7A] hover:text-[#1B2A57]">
                                        View All
                                    </a>

                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-sm font-medium text-slate-600 hover:text-[#0B1533]">
                                                Mark all read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endcan

                    @if(Auth::user()->roles->count() > 1)
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 rounded-2xl bg-[#F8FAFF] border border-[#E6EDFF] px-3 py-2.5 text-sm font-medium text-[#0B1533] hover:bg-[#F2F6FF] transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    <span class="hidden sm:inline">{{ ucfirst(Auth::user()->active_role) }}</span>
                                    <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                    Switch Role
                                </div>
                                @foreach(Auth::user()->roles as $role)
                                    <form method="POST" action="{{ route('role.switch') }}">
                                        @csrf
                                        <input type="hidden" name="role" value="{{ $role->name }}">
                                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition {{ Auth::user()->active_role === $role->name ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                            {{ ucfirst($role->name) }}
                                        </button>
                                    </form>
                                @endforeach
                            </x-slot>
                        </x-dropdown>
                    @endif

                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 rounded-2xl bg-[#F8FAFF] border border-[#E6EDFF] px-3 py-2.5 text-sm font-medium text-[#0B1533] hover:bg-[#F2F6FF] transition">
                                <div class="w-9 h-9 rounded-2xl bg-gradient-to-br from-[#1B2A57] to-[#2E3F7A] text-white flex items-center justify-center font-bold shadow-sm">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>

                                <div class="hidden sm:block text-left leading-tight">
                                    <div class="font-semibold text-[#0B1533]">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-500">Account</div>
                                </div>

                                <svg class="fill-current h-4 w-4 text-slate-500" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @can('view notifications')
                                <x-dropdown-link :href="route('notifications.index')">
                                    {{ __('Notifications') }}
                                    @if($unreadCount > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-[#2E3F7A] rounded-full">
                                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                        </span>
                                    @endif
                                </x-dropdown-link>
                            @endcan

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</div>