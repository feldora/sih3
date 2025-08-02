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
    <h4 class="pb-2 pb-2 text-center">{{ $fileName }}</h4>
    <div class="flex flex-col items-center space-y-4 h-full">
        <iframe src="{{ $mediaUrl }}#toolbar=0" type="application/pdf" class="w-full h-full" style="border: none;"></iframe>
    </div>
</div>
