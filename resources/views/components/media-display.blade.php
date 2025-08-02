{{-- resources/views/components/media-display.blade.php --}}
@props([
    'media' => null,
    'fallbackImage' => '/images/sih3.png',
    'containerClass' => 'media-container rounded-lg overflow-hidden mb-6 bg-gray-50'
])

@php
    $mediaType = $media ? strtolower(pathinfo($media->file_name ?? '', PATHINFO_EXTENSION)) : null;
    $mediaUrl = $media && $media->original_url ? $media->original_url : $fallbackImage;
    $fileName = $media->file_name ?? 'Default Image';
    $fileSize = $media->size ?? null;
    
    // Format file size
    $formattedSize = '';
    if ($fileSize) {
        if ($fileSize < 1024) {
            $formattedSize = $fileSize . ' B';
        } elseif ($fileSize < 1048576) {
            $formattedSize = round($fileSize / 1024, 1) . ' KB';
        } else {
            $formattedSize = round($fileSize / 1048576, 1) . ' MB';
        }
    }
@endphp

<div class="{{ $containerClass }}">
    @if($media)
        @switch($mediaType)
            {{-- Image Files --}}
            @case('jpg')
            @case('jpeg')
            @case('png')
            @case('gif')
            @case('webp')
            @case('svg')
                <div class="image-container">
                    <img src="{{ $mediaUrl }}" alt="{{ $fileName }}" 
                         class="rounded w-full object-cover">
                    @if($formattedSize)
                        {{-- <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                            {{ $formattedSize }}
                        </div> --}}
                    @endif
                </div>
                @break

            {{-- PDF Files --}}
            @case('pdf')
                <x-media-display.pdf-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="PDF Document"
                    iconColor="red"
                    buttonColor="red">
                </x-media-display.pdf-card>
                @break

            {{-- Excel Files --}}
            @case('xlsx')
            @case('xls')
            @case('csv')
                <x-media-display.xlsx-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    :fileType="$mediaType === 'csv' ? 'CSV Spreadsheet' : 'Excel Spreadsheet'"
                    iconColor="green"
                    buttonColor="green">
                </x-media-display.xlsx-card>
                @break

            {{-- Word Documents --}}
            @case('doc')
            @case('docx')
                <x-media-display.word-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="Word Document"
                    iconColor="blue"
                    buttonColor="blue">
                </x-media-display.word-card>
                @break

            {{-- PowerPoint Files --}}
            @case('ppt')
            @case('pptx')
                <x-media-display.pptx-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="PowerPoint Presentation"
                    iconColor="orange"
                    buttonColor="orange">
                </x-media-display.pptx-card>
                @break

            {{-- Video Files --}}
            @case('mp4')
            @case('avi')
            @case('mov')
            @case('wmv')
            @case('webm')
                <x-media-display.video-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    :autoplay="true"
                    :loop="false"
                    :controls="true"
                    fileType="{{ $mediaType }}"
                    iconColor="orange"
                    buttonColor="orange">
                </x-media-display.video-card>
                
                @break

            {{-- Audio Files --}}
            @case('mp3')
            @case('wav')
            @case('ogg')
            @case('m4a')
                <x-media-display.audio-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="PowerPoint Presentation"
                    iconColor="orange"
                    buttonColor="orange">
                </x-media-display.audio-card>
                @break

            {{-- Text Files --}}
            @case('txt')
            @case('rtf')
                <x-media-display.text-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="PowerPoint Presentation"
                    iconColor="orange"
                    buttonColor="orange">
                </x-media-display.text-card>
                @break

            {{-- Archive Files --}}
            @case('zip')
            @case('rar')
            @case('7z')
                <x-media-display.file-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    fileType="Archive File"
                    iconColor="yellow"
                    buttonColor="yellow"
                    :showViewButton="false">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </x-media-display.file-card>
                @break

            {{-- Default for unknown file types --}}
            @default
                <x-media-display.file-card 
                    :fileName="$fileName"
                    :mediaUrl="$mediaUrl"
                    :fileSize="$formattedSize"
                    :fileType="strtoupper($mediaType) . ' File'"
                    iconColor="gray"
                    buttonColor="gray">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </x-media-display.file-card>
        @endswitch
    @else
        {{-- Fallback when no media --}}
        <img src="{{ $fallbackImage }}" alt="Default Image" 
             class="rounded w-full object-cover">
    @endif
</div>