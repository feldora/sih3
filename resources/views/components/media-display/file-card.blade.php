{{-- resources/views/components/media-display/file-card.blade.php --}}
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

<div class="file-container bg-white border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-{{ $iconColor }}-300 transition-colors duration-300">
    <div class="flex flex-col items-center space-y-4">
        <!-- File Icon -->
        <div class="w-16 h-16 bg-{{ $iconColor }}-100 rounded-full flex items-center justify-center transition-transform duration-300 hover:scale-110">
            {{ $slot }}
        </div>

        <!-- File Info -->
        <div class="max-w-full">
            <h4 class="font-semibold text-gray-800 truncate max-w-xs" title="{{ $fileName }}">
                {{ $fileName }}
            </h4>
            <div class="flex items-center justify-center space-x-2 text-sm text-gray-600 mt-1">
                <span>{{ $fileType }}</span>
                @if($fileSize)
                    <span>•</span>
                    <span>{{ $fileSize }}</span>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap justify-center gap-3">
            @if($showViewButton)
                <a href="{{ $mediaUrl }}" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-{{ $buttonColor }}-600 text-white rounded-lg hover:bg-{{ $buttonColor }}-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="text-sm font-medium">View</span>
                </a>
            @endif

            @if($showDownloadButton)
                <a href="{{ $mediaUrl }}" download 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-medium">Download</span>
                </a>
            @endif
        </div>
    </div>
</div>