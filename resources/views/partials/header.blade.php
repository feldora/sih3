<style>
    #bgAnimation {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: -1;
        overflow: hidden;
    }

    #bgAnimation li {
        position: absolute;
        display: block;
        list-style: none;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.4);
        bottom: -200px;
        animation: bgAni 25s infinite linear;
    }

    #bgAnimation li:nth-child(1) {
        width: 20px;
        height: 20px;
        left: 95%;
        animation-delay: 10s;
    }

    #bgAnimation li:nth-child(2) {
        width: 80px;
        height: 80px;
        left: 25%;
        animation-delay: 0s;
    }

    #bgAnimation li:nth-child(3) {
        width: 70px;
        height: 70px;
        left: 75%;
        animation-delay: 18s;
    }

    #bgAnimation li:nth-child(4) {
        width: 30px;
        height: 30px;
        left: 10%;
        animation-delay: 5s;
    }

    #bgAnimation li:nth-child(5) {
        width: 40px;
        height: 40px;
        left: 84%;
        animation-delay: 1s;
    }

    #bgAnimation li:nth-child(6) {
        width: 50px;
        height: 50px;
        left: 50%;
        animation-delay: 15s;
    }

    #bgAnimation li:nth-child(7) {
        width: 100px;
        height: 100px;
        left: 40%;
        animation-delay: 6s;
    }

    #bgAnimation li:nth-child(8) {
        width: 60px;
        height: 60px;
        left: 85%;
        animation-delay: 17s;
    }

    @keyframes bgAni {
        from {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
            border-radius: 0;
        }

        to {
            transform: translateY(-1000px) rotate(720deg);
            opacity: 0;
            border-radius: 80%;
        }
    }
</style>
<header class="sticky top-0 z-50 bg-gray-900 text-white shadow-md">
    <div class="navbar container mx-auto">
        <!-- Logo -->
        <div class="navbar-start">
            <a href="/" class="btn btn-ghost normal-case text-2xl font-bold">SIH3</a>
        </div>

        <!-- Navigation Links -->
        <div class="navbar-center hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                @auth
                    <li><a href="{{ url('/dashboard') }}" class="hover:text-green-400">Dashboard</a></li>
                @endauth
                @foreach ($menus_public as $menu)
                    <li class="{{ $menu->children->count() > 0 ? 'relative' : '' }}">
                        @if ($menu->children->count() > 0)
                            <details>
                                <summary class="hover:text-green-400 flex items-center">
                                    @if ($menu->icon)
                                        <i class="{{ $menu->icon }} text-xl mr-2"></i>
                                    @endif
                                    {{ $menu->title }}
                                </summary>
                                <ul class="p-2 bg-gray-900 text-white">
                                    @foreach ($menu->children as $child)
                                        <li>
                                            <a href="{{ url($child->url) }}" class="hover:text-green-400 flex items-center">
                                                @if ($child->icon)
                                                    <i class="{{ $child->icon }} text-lg mr-2"></i>
                                                @endif
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </details>
                        @else
                            <a href="{{ url($menu->url) }}" class="hover:text-green-400 flex items-center">
                                @if ($menu->icon)
                                    <i class="{{ $menu->icon }} text-xl mr-2"></i>
                                @endif
                                {{ $menu->title }}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Login Button -->
        <div class="navbar-end">
            @if (Route::has('login'))
                @auth
                    <!-- Tambahkan menu pengguna jika diperlukan -->
                @else
                    <button id="loginDrawerButton" class="btn btn-outline btn-success">Log in</button>
                @endauth
            @endif
        </div>
    </div>

    <!-- Drawer Overlay -->
    <div id="loginDrawerOverlay" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40 hidden"></div>

    <!-- Drawer -->
    <div id="loginDrawer"
        class="fixed top-0 right-0 h-full w-80 bg-gradient-to-b from-blue-400/60 via-cyan-400/50 to-blue-600/60 shadow-lg z-50 backdrop-blur-md transform translate-x-full transition-transform duration-300 ease-in-out border-l border-white/20">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">Login</h2>
                <button id="closeLoginDrawer"
                    class="text-blue-100 hover:text-white text-2xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm mb-1 text-white">Email</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="w-full px-3 py-2 rounded bg-blue-200/30 bg-opacity-30 text-white placeholder-blue-100 focus:outline-none focus:ring focus:border-cyan-300">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm mb-1 text-white">Password</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2 rounded bg-blue-200/30 bg-opacity-30 text-white placeholder-blue-100 focus:outline-none focus:ring focus:border-cyan-300">
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2 accent-cyan-400">
                    <label for="remember" class="text-sm text-white">Remember me</label>
                </div>
                <button type="submit"
                    class="w-full bg-cyan-500/80 hover:bg-cyan-600/80 text-white py-2 rounded shadow">Log in</button>
            </form>
        </div>
        <ul class="bg-bubbles" id="bgAnimation">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

    <script>
        const loginDrawerButton = document.getElementById('loginDrawerButton');
        const loginDrawer = document.getElementById('loginDrawer');
        const loginDrawerOverlay = document.getElementById('loginDrawerOverlay');
        const closeLoginDrawer = document.getElementById('closeLoginDrawer');

        loginDrawerButton.addEventListener('click', () => {
            loginDrawer.classList.remove('translate-x-full');
            loginDrawerOverlay.classList.remove('hidden');
        });

        closeLoginDrawer.addEventListener('click', () => {
            loginDrawer.classList.add('translate-x-full');
            loginDrawerOverlay.classList.add('hidden');
        });

        loginDrawerOverlay.addEventListener('click', () => {
            loginDrawer.classList.add('translate-x-full');
            loginDrawerOverlay.classList.add('hidden');
        });
    </script>
