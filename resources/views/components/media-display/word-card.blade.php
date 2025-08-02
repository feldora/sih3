@props([
    'fileName',
    'mediaUrl',
    'fileSize' => null,
    'fileType',
    'iconColor' => 'gray',
    'buttonColor' => 'gray',
    'showViewButton' => true,
    'showDownloadButton' => true
])

<div class="file-container bg-white border-2 border-dashed border-gray-300 
             rounded-lg p-2 hover:border-{{ $iconColor }}-300 transition-colors duration-300 h-screen overflow-auto">
    <h4 class="pb-2 text-center">{{ $fileName }}</h4>
    <hr>
    <div class="flex flex-col items-center space-y-4 h-full">
        <div id="word-output" class="prose max-w-none w-full px-4 py-2"></div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
<script>
let mediaUrl = "{{ $mediaUrl }}";
let fileName = "{{ $fileName }}";

// Fetch and render Word DOCX file using Mammoth.js
fetch(mediaUrl)
  .then(response => response.arrayBuffer())
  .then(arrayBuffer => {
    mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
      .then(function(result) {
        document.getElementById("word-output").innerHTML = result.value;
      })
      .catch(function(error) {
        console.error("Gagal konversi file Word:", error);
        document.getElementById("word-output").innerHTML = 
          "<p class='text-red-500'>Gagal menampilkan file Word.</p>";
      });
  })
  .catch(error => {
    console.error("Gagal memuat file Word:", error);
    document.getElementById("word-output").innerHTML = 
      "<p class='text-red-500'>Gagal memuat file Word.</p>";
  });
</script>
@endpush
