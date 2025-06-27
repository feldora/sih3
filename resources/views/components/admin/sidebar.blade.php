<aside id="sidebar" class="hidden md:flex flex-col w-64 shadow-lg transition duration-300 ease-in-out bg-white z-50 h-full">
    <div class="p-4 text-xl font-bold border-b ">
        SIH3 <br>
        <span class="text-sm text-gray-700 dark:text-gray-300 hidden md:inline">Prov. Sulawesi Tengah</span>
    </div>
    <nav class="p-4 flex-1" role="navigation" aria-label="Sidebar">
        <ul class="menu rounded-box w-full">
            @foreach ($menus_admin as $menu)
                @if ($menu->title === "Pengaturan")
                    @php
                        $pengaturan = $menu;
                    @endphp
                    @continue
                @endif
                @if ($menu->children->count() > 0)
                    @php
                        $uriChildren = $menu->children->map(function ($child) {
                            return url($child->url);
                        })->toArray();
                        $isActive = in_array(request()->url(), $uriChildren);
                    @endphp
                    <li class="pt-2">
                        <details  {{ $isActive ? 'open' : '' }} >
                            <summary class="text-blue-900 ">
                                @if ($menu->icon)
                                    <i class="{{ $menu->icon }} text-xl mr-3"></i>
                                @endif
                                {{ $menu->title }}
                            </summary>
                            <ul>
                                @foreach ($menu->children as $child)
                                    <li class="pt-2">
                                        <a href="{{ url($child->url) }}"
                                            class="{{ request()->url() === url($child->url) ? 'bg-blue-700/80 text-white font-semibold' : 'text-blue-900' }}">
                                            @if ($child->icon)
                                                <i class="{{ $child->icon }} text-lg mr-3"></i>
                                            @endif
                                            {{ $child->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </details>
                    </li>
                @else
                    <li class="pt-2">
                        <a href="{{ url($menu->url) }}"
                            class="{{ request()->url() === url($menu->url) ? 'bg-blue-700/80 text-white font-semibold' : 'text-blue-900' }}">
                            @if ($menu->icon)
                                <i class="{{ $menu->icon }} text-xl mr-3"></i>
                            @endif
                            {{ $menu->title }}
                        </a>
                    </li>
                @endif
            @endforeach
            @if (isset($pengaturan))
                <div class="divider"></div>
                <li class="pt-2">
                    <details {{ request()->url() === url($pengaturan->url) ? 'open' : '' }}>
                        <summary class="text-blue-900 ">
                            @if ($pengaturan->icon)
                                <i class="{{ $pengaturan->icon }} text-xl mr-3"></i>
                            @endif
                            {{ $pengaturan->title }}
                        </summary>
                        <ul>
                            @foreach ($pengaturan->children as $child)
                                <li class="pt-2">
                                    <a href="{{ url($child->url) }}"
                                        class="{{ request()->url() === url($child->url) ? 'bg-blue-700/80 text-white font-semibold' : 'text-blue-900' }}">
                                        @if ($child->icon)
                                            <i class="{{ $child->icon }} text-lg mr-3"></i>
                                        @endif
                                        {{ $child->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                </li>
            @endif
        </ul>
    </nav>
    <div class="mt-auto p-4 border-t border-base-300 rounded-b-xl w-full">
        <!-- User Menu -->
        <div class="relative w-full block md:hidden">
            <button type="button"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-50 transition focus:outline-none"
                id="user-menu-button_mobile" aria-haspopup="true" aria-expanded="false">
                <img src="/images/boy.png" alt="Avatar"
                    class="w-9 h-9 rounded-full border-2 border-blue-200" />
                <div class="flex-1 text-left">
                    <span class="block font-semibold text-blue-900">{{ Auth::user()->name }}</span>
                    <span class="block text-xs text-gray-500 truncate">{{ Auth::user()->email }}</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <ul class="absolute left-0 bottom-12 w-full bg-base-100 rounded-lg shadow-lg py-2 z-50 hidden"
                id="user-menu-dropdown_mobile">
                <li>
                    <a href="{{ route('admin.profile.edit', auth()->user()->id) }}"
                        class="block px-4 py-2 text-gray-700 hover:bg-blue-100 rounded-t-lg transition">Profil</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-b-lg transition">Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
        @push('scripts')
            <script>
                // Simple dropdown toggle
                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.getElementById('user-menu-button_mobile');
                    const dropdown = document.getElementById('user-menu-dropdown_mobile');
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdown.classList.toggle('hidden');
                    });
                    document.addEventListener('click', function() {
                        dropdown.classList.add('hidden');
                    });
                });
            </script>
        @endpush
    </div>
</aside>
