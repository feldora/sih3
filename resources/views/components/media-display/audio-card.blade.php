@props([
    'fileName',
    'mediaUrl',
    'fileType',
    'iconColor' => 'gray',
])

<div class="file-container bg-white border-2 border-dashed border-gray-300 
             rounded-lg p-4 hover:border-{{ $iconColor }}-300 transition-colors duration-300 h-screen">
    <h4 class="pb-2 text-center">{{ $fileName }}</h4>
    <hr>
    <div class="w-full h-full flex justify-center items-center">
        <audio controls class="w-full">
            <source src="{{ $mediaUrl }}" type="audio/{{ strtolower($fileType) }}">
            Browser Anda tidak mendukung audio.
        </audio>
    </div>
</div>
