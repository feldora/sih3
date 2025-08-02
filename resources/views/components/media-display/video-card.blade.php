@props([
    'fileName',
    'mediaUrl',
    'fileType',
    'iconColor' => 'gray',
    'autoplay' => false,
    'loop' => false,
    'controls' => true
])

<div class="file-container bg-white border-2 border-dashed border-gray-300 
             rounded-lg p-4 hover:border-{{ $iconColor }}-300 transition-colors duration-300 ">
    <h4 class="pb-2 text-center">{{ $fileName }}</h4>
    <hr>
    <div class="w-full h-full flex justify-center items-center">
        <video controls class="w-full max-h-[80vh]" preload="metadata">
            <source src="{{ $mediaUrl }}" type="video/{{ strtolower($fileType) }}" {{ $autoplay ? 'autoplay' : '' }} {{ $loop ? 'loop' : '' }} {{ $controls ? 'controls' : '' }}>
            Browser Anda tidak mendukung video.
        </video>
    </div>
</div>
