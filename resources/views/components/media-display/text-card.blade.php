@props([
    'fileName',
    'mediaUrl',
    'fileType',
    'iconColor' => 'gray',
])

<div class="file-container bg-white border-2 border-dashed border-gray-300 
             rounded-lg p-4 hover:border-{{ $iconColor }}-300 transition-colors duration-300 h-screen overflow-auto">
    <h4 class="pb-2 text-center">{{ $fileName }}</h4>
    <hr>
    <pre id="text-output" class="text-sm bg-gray-100 p-4 rounded w-full h-full overflow-auto">Memuat file teks...</pre>
</div>

@push('scripts')
<script>
fetch("{{ $mediaUrl }}")
  .then(res => res.text())
  .then(text => {
    document.getElementById("text-output").textContent = text;
  })
  .catch(err => {
    console.error("Gagal memuat file teks:", err);
    document.getElementById("text-output").textContent = "Gagal menampilkan isi file teks.";
  });
</script>
@endpush
