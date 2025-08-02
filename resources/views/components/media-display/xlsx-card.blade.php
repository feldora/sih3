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

@php
    // Pengecekan sederhana apakah ini PDF
    $isPdf = Str::lower((string) $fileType) === 'pdf';
@endphp
<div class="file-container bg-white border-2 border-dashed border-gray-300 
             rounded-lg p-2 hover:border-{{ $iconColor }}-300 transition-colors duration-300 h-screen">
    <h4 class="pb-2 text-center">{{ $fileName }}</h4>
    <hr>
    <div class="flex flex-col items-center space-y-4 h-full">
        <div id="xlsx-output"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
<script>
let mediaUrl = "{{ $mediaUrl }}";
let fileName = "{{ $fileName }}";

// Fetch the Excel file from mediaUrl and display it
fetch(mediaUrl)
  .then(response => response.arrayBuffer())
  .then(arrayBuffer => {
    const data = new Uint8Array(arrayBuffer);
    const workbook = XLSX.read(data, { type: "array" });

    // Ambil sheet pertama
    const firstSheet = workbook.SheetNames[0];
    const worksheet = workbook.Sheets[firstSheet];

    // Convert to HTML
    const html = XLSX.utils.sheet_to_html(worksheet, {
      editable: false,
    //   header: fileName
    });

    // Tampilkan
    document.getElementById("xlsx-output").innerHTML = html;
  })
  .catch(error => {
    console.error("Gagal memuat file Excel:", error);
    document.getElementById("xlsx-output").innerHTML = 
      "<p class='text-red-500'>Gagal menampilkan file Excel.</p>";
  });
</script>
@endpush
