<header class="flex items-center justify-between p-4 bg-base-100 shadow m-3 rounded-lg">
  <div class="flex items-center space-x-4">
    <div class="flex items-center gap-2">
      <button type="button" class="text-black dark:text-white md:hidden text-base-content focus:outline-none menu-toggle" aria-label="Toggle Sidebar">
        ☰
      </button>
      <div class="hidden sm:block">
        <nav aria-label="breadcrumb" class="w-full py-1 px-2">
          <ol class="flex space-x-3">
            <li class="flex items-center">
              <a href="javascript:;" class="flex items-center text-black/40 dark:text-white/40 hover:text-black dark:hover:text-white">Dashboard</a>
            </li>
            @php
              $segments = request()->segments();
              $url = url('/');
            @endphp
            @foreach ($segments as $index => $segment)
              <li class="flex items-center space-x-1">
                <span class="text-black/40 dark:text-white/40">/</span>
                @php
                  $url .= '/' . $segment;
                  $isLast = $index === array_key_last($segments);
                @endphp
                @if ($isLast)
                  <span class="flex items-center px-3 text-black dark:text-white capitalize">{{ $segment }}</span>
                @else
                  <a href="{{ $url }}" class="flex items-center px-3 text-black dark:text-white/70 hover:text-black dark:hover:text-white capitalize">{{ $segment }}</a>
                @endif
              </li>
            @endforeach
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <div class="relative hidden md:block">
    <button id="user-menu-button" class="flex items-center space-x-2 focus:outline-none user-menu-button" aria-haspopup="true" aria-expanded="false">
      <img src="/images/boy.png" alt="Avatar" class="w-8 h-8 rounded-full" />
      <span>{{ Auth::user()->name }}</span>
    </button>

    @include('components.admin.user-dropdown')
  </div>
</header>
