<div class="media-picker w-full">
    <!-- Selected Media Display -->
    @if(count($selectedMediaIds) > 0)
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Media Terpilih ({{ count($selectedMediaIds) }})</span>
                <button 
                    wire:click="clearSelection" 
                    class="btn btn-xs btn-ghost text-error"
                    type="button"
                >
                    Clear All
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($this->selectedMediaProperty as $media)
                    <div class="relative">
                        <img 
                            src="{{ $media->getUrl() }}" 
                            alt="{{ $media->name }}"
                            class="w-16 h-16 object-cover rounded border-2 border-primary"
                        >
                        <button 
                            wire:click="removeFromSelection({{ $media->id }})"
                            class="absolute -top-1 -right-1 btn btn-circle btn-xs btn-error"
                            type="button"
                        >
                            ×
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Picker Button -->
    <button 
        wire:click="open" 
        class="btn btn-outline w-full {{ count($selectedMediaIds) > 0 ? 'btn-primary' : '' }}"
        type="button"
    >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        {{ count($selectedMediaIds) > 0 ? 'Ubah Media ('.count($selectedMediaIds).')' : 'Pilih Media' }}
    </button>

    <!-- Media Picker Modal -->
    @if($isOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-base-100 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <!-- Header -->
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold">Pilih Media</h3>
                    <button 
                        wire:click="close" 
                        class="btn btn-sm btn-ghost btn-circle"
                        type="button"
                    >
                        ×
                    </button>
                </div>

                <div class="p-4 overflow-y-auto max-h-[calc(90vh-8rem)]">
                    <!-- Upload Section -->
                    @if($showUpload)
                        <div class="mb-6">
                            <div class="border-2 border-dashed border-base-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                <input 
                                    type="file" 
                                    wire:model="tempFiles" 
                                    {{ $multiple ? 'multiple' : '' }}
                                    accept="{{ $accept }}"
                                    class="hidden" 
                                    id="file-upload-{{ uniqid() }}"
                                >
                                
                                <label for="file-upload-{{ uniqid() }}" class="cursor-pointer">
                                    <svg class="w-12 h-12 text-base-content/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="font-medium">Upload File Baru</p>
                                    <p class="text-sm text-base-content/60">{{ $accept }} - Max {{ $maxFileSize }}KB</p>
                                </label>
                            </div>

                            <!-- Temp Files Preview & Upload Button -->
                            @if(!empty($tempFiles))
                                <div class="mt-4">
                                    <div class="grid {{ $gridCols }} gap-2 mb-4">
                                        @foreach($tempFiles as $file)
                                            <img 
                                                src="{{ $file->temporaryUrl() }}" 
                                                alt="Preview"
                                                class="w-full h-20 object-cover rounded border"
                                            >
                                        @endforeach
                                    </div>
                                    <button 
                                        wire:click="uploadFiles" 
                                        class="btn btn-primary btn-sm"
                                        wire:loading.attr="disabled"
                                        wire:target="uploadFiles"
                                        type="button"
                                    >
                                        <span wire:loading.remove wire:target="uploadFiles">Upload Files</span>
                                        <span wire:loading wire:target="uploadFiles">Uploading...</span>
                                    </button>
                                </div>
                            @endif

                            <!-- Upload Loading -->
                            <div wire:loading wire:target="tempFiles" class="mt-2">
                                <div class="flex items-center space-x-2">
                                    <span class="loading loading-spinner loading-sm"></span>
                                    <span class="text-sm">Processing files...</span>
                                </div>
                            </div>

                            <!-- Errors -->
                            @error('tempFiles.*')
                                <div class="alert alert-error mt-2">
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    @endif

                    <!-- Media Gallery -->
                    @if($existingMedia && $existingMedia->count() > 0)
                        <div class="grid {{ $gridCols }} gap-4">
                            @foreach($existingMedia as $media)
                                <div class="relative group">
                                    <div 
                                        class="relative cursor-pointer"
                                        wire:click="selectMedia({{ $media->id }})"
                                    >
                                        <img 
                                            src="{{ $media->getUrl() }}" 
                                            alt="{{ $media->name }}"
                                            class="w-full h-24 object-cover rounded border-2 transition-all
                                                {{ in_array($media->id, $selectedMediaIds) ? 'border-primary ring-2 ring-primary ring-opacity-50' : 'border-base-300 hover:border-primary' }}"
                                        >
                                        
                                        <!-- Selection Badge -->
                                        @if(in_array($media->id, $selectedMediaIds))
                                            <div class="absolute top-1 right-1 bg-primary text-primary-content rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">
                                                ✓
                                            </div>
                                        @endif

                                        <!-- Delete Button -->
                                        @if($showDelete)
                                            <button 
                                                wire:click.stop="deleteMedia({{ $media->id }})"
                                                wire:confirm="Hapus media ini?"
                                                class="absolute top-1 left-1 btn btn-circle btn-xs btn-error opacity-0 group-hover:opacity-100 transition-opacity"
                                                type="button"
                                            >
                                                🗑
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Media Info -->
                                    <div class="mt-1 text-xs text-center">
                                        <p class="truncate" title="{{ $media->name }}">{{ $media->name }}</p>
                                        <p class="text-base-content/60">{{ $media->human_readable_size }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-base-content/60">
                            <p>Tidak ada media tersedia</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="flex justify-between items-center p-4 border-t bg-base-50">
                    <span class="text-sm text-base-content/60">
                        {{ count($selectedMediaIds) }} media terpilih
                        @if(!$multiple && count($selectedMediaIds) >= 1)
                            (maksimal 1)
                        @elseif($multiple)
                            (maksimal {{ $maxFiles }})
                        @endif
                    </span>
                    
                    <div class="flex gap-2">
                        @if(count($selectedMediaIds) > 0)
                            <button 
                                wire:click="clearSelection" 
                                class="btn btn-sm btn-ghost"
                                type="button"
                            >
                                Clear
                            </button>
                        @endif
                        <button 
                            wire:click="close" 
                            class="btn btn-sm btn-primary"
                            type="button"
                        >
                            Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Messages -->
    @if (session()->has('upload-success'))
        <div class="alert alert-success mt-2">
            <span>{{ session('upload-success') }}</span>
        </div>
    @endif

    @if (session()->has('delete-success'))
        <div class="alert alert-success mt-2">
            <span>{{ session('delete-success') }}</span>
        </div>
    @endif
</div>