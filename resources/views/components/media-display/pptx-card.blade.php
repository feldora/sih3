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
    <div id="pptx-output" class="prose max-w-none w-full px-4 py-2">Memuat presentasi...</div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
fetch("{{ $mediaUrl }}")
  .then(res => res.arrayBuffer())
  .then(async buffer => {
    const zip = await JSZip.loadAsync(buffer);
    const slides = Object.keys(zip.files).filter(name => name.match(/^ppt\/slides\/slide\d+\.xml$/));
    slides.sort((a, b) => a.localeCompare(b, undefined, { numeric: true }));

    const slideTexts = await Promise.all(
      slides.map(async slidePath => {
        const xml = await zip.files[slidePath].async("text");
        const matches = [...xml.matchAll(/<a:t>(.*?)<\/a:t>/g)];
        return matches.map(m => m[1]).join(" ");
      })
    );

    const html = slideTexts.map((text, i) => `<div class="mb-4"><strong>Slide ${i + 1}:</strong><br>${text}</div>`).join("");
    document.getElementById("pptx-output").innerHTML = html || '<p class="text-gray-500">Tidak ada teks ditemukan.</p>';
  })
  .catch(err => {
    console.error("Gagal memuat PPTX:", err);
    document.getElementById("pptx-output").innerHTML = '<p class="text-red-500">Gagal memuat file presentasi.</p>';
  });
</script>
@endpush
