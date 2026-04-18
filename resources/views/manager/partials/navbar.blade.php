@php
    $user = auth()->user();
    $nav = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['label' => 'Suppliers', 'route' => 'suppliers.index', 'active' => request()->routeIs('suppliers.*')],
        ['label' => 'Inventory', 'route' => 'layups.index', 'active' => request()->routeIs('layups.*')],
    ];
    if ($user && $user->isAdmin()) {
        $nav[] = ['label' => 'Users', 'route' => 'users.index', 'active' => request()->routeIs('users.*')];
    }
@endphp
<nav x-data="{ open: false, profile: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold">C</div>
                    <span class="font-semibold text-gray-900 hidden sm:inline">CLT Toolbox Manager</span>
                </a>
                <div class="hidden md:flex items-center gap-1">
                    @foreach ($nav as $item)
                        <a href="{{ route($item['route']) }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition {{ $item['active'] ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0a3 3 0 11-6 0"/>
                    </svg>
                </button>

                @if ($user)
                <div class="relative" @click.outside="profile = false">
                    <button @click="profile = !profile" class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white text-xs font-semibold flex items-center justify-center">
                            {{ $user->initials() }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-sm font-medium text-gray-900 leading-tight">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 capitalize leading-tight">{{ $user->role }}</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <div x-show="profile" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-200 py-1 z-20">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <div class="flex items-center justify-between gap-2">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                @php
                                    $roleColors = [
                                        'admin'  => 'bg-rose-100 text-rose-700',
                                        'staff'  => 'bg-emerald-100 text-emerald-700',
                                        'viewer' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $roleClass = $roleColors[$user->role] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $roleClass }}">
                                    {{ $user->role }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $user->email }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Log Out</button>
                        </form>
                    </div>
                </div>
                @endif

                <button @click="open = !open" class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak class="md:hidden border-t border-gray-100 bg-white">
        <div class="px-2 py-2 space-y-1">
            @foreach ($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium {{ $item['active'] ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:bg-gray-50' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
