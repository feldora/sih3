<section class="bg-base-100 rounded shadow">
  <div class="p-4 border-b border-base-300">
    <h2 class="text-lg font-semibold">Aktivitas Terbaru</h2>
  </div>
  <ul class="divide-y divide-base-300">
    @foreach ($activities as $activity)
      <li class="p-4 flex items-center">
        <img src="{{ $activity['avatar'] }}" alt="Avatar" class="w-8 h-8 rounded-full mr-4" />
        <div>
          <p class="font-medium">{{ $activity['text'] }}</p>
          <p class="text-sm text-base-content/70">{{ $activity['time'] }}</p>
        </div>
      </li>
    @endforeach
  </ul>
</section>
