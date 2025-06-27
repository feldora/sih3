<div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-base-100 rounded-md shadow-lg py-1 hidden z-20 transition duration-200 ease-in-out" role="menu" aria-label="User Menu">
  <a href="{{ route('admin.profile.edit', auth()->user()->id) }}" class="block px-4 py-2 text-sm text-base-content hover:bg-base-200" role="menuitem">Profil Saya</a>
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-base-content hover:bg-base-200" role="menuitem">
      Keluar
    </button>
  </form>
</div>