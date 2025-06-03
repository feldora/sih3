<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPicker extends Component
{
    use WithFileUploads;

    // Input properties
    public $selectedMediaIds = [];
    public $multiple = false;
    public $accept = 'image/*';
    public $maxFiles = 10;
    public $maxFileSize = 2048; // KB
    public $gridCols = 'grid-cols-2 md:grid-cols-4';
    public $mediaFilter = [];
    public $showUpload = true;
    public $showDelete = true;
    
    // Internal properties
    public $tempFiles = [];
    public $existingMedia;
    public $isOpen = false;

    protected $listeners = [
        'openMediaPicker' => 'open',
        'resetMediaPicker' => 'resetPicker'
    ];

    public function mount()
    {
        $this->loadExistingMedia();
        
        // Ensure selectedMediaIds is array
        if (is_string($this->selectedMediaIds)) {
            $this->selectedMediaIds = $this->selectedMediaIds ? explode(',', $this->selectedMediaIds) : [];
        }
        
        $this->selectedMediaIds = array_map('intval', array_filter($this->selectedMediaIds));
    }

    public function loadExistingMedia()
    {
        $query = Media::query()->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($this->mediaFilter)) {
            foreach ($this->mediaFilter as $key => $value) {
                if ($key === 'mime_type' && is_array($value)) {
                    $query->whereIn('mime_type', $value);
                } elseif (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        $this->existingMedia = $query->get();
    }

    public function updatedTempFiles()
    {
        $rules = [];
        foreach ($this->tempFiles as $index => $file) {
            $rules["tempFiles.{$index}"] = "max:{$this->maxFileSize}";
            
            if (str_contains($this->accept, 'image')) {
                $rules["tempFiles.{$index}"] .= '|image';
            }
        }

        $this->validate($rules);
    }

    public function uploadFiles()
    {
        if (empty($this->tempFiles)) return;

        $newMediaIds = [];
        
        foreach ($this->tempFiles as $file) {
            // Create temporary model to handle file upload
            $tempModel = new class extends \Illuminate\Database\Eloquent\Model implements \Spatie\MediaLibrary\HasMedia {
                use \Spatie\MediaLibrary\InteractsWithMedia;
                protected $table = 'temp_uploads'; // This won't be used
            };

            // Add file to media library
            $media = $tempModel->addMedia($file->getRealPath())
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection();

            $newMediaIds[] = $media->id;
        }

        // Add new media IDs to selection
        if ($this->multiple) {
            $this->selectedMediaIds = array_merge($this->selectedMediaIds, $newMediaIds);
        } else {
            $this->selectedMediaIds = [$newMediaIds[0]];
        }

        // Clear temp files
        $this->tempFiles = [];
        
        // Refresh media list
        $this->loadExistingMedia();
        
        // Emit the updated selection
        $this->emitSelection();
        
        session()->flash('upload-success', 'File berhasil diupload!');
    }

    public function selectMedia($mediaId)
    {
        if ($this->multiple) {
            if (in_array($mediaId, $this->selectedMediaIds)) {
                $this->selectedMediaIds = array_diff($this->selectedMediaIds, [$mediaId]);
            } else {
                if (count($this->selectedMediaIds) < $this->maxFiles) {
                    $this->selectedMediaIds[] = $mediaId;
                }
            }
        } else {
            $this->selectedMediaIds = in_array($mediaId, $this->selectedMediaIds) ? [] : [$mediaId];
        }

        $this->emitSelection();
    }

    public function removeFromSelection($mediaId)
    {
        $this->selectedMediaIds = array_diff($this->selectedMediaIds, [$mediaId]);
        $this->emitSelection();
    }

    public function deleteMedia($mediaId)
    {
        $media = Media::find($mediaId);
        if ($media) {
            $media->delete();
            $this->removeFromSelection($mediaId);
            $this->loadExistingMedia();
            
            session()->flash('delete-success', 'Media berhasil dihapus!');
        }
    }

    public function clearSelection()
    {
        $this->selectedMediaIds = [];
        $this->emitSelection();
    }

    public function open()
    {
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
    }

    public function resetPicker()
    {
        $this->selectedMediaIds = [];
        $this->tempFiles = [];
        $this->loadExistingMedia();
    }

    private function emitSelection()
    {
        // Only emit the selected media IDs - controller will handle the rest
        $this->dispatch('mediaPickerUpdated', [
            'selectedIds' => array_values($this->selectedMediaIds),
            'count' => count($this->selectedMediaIds)
        ]);
    }

    public function getSelectedMediaProperty()
    {
        return Media::whereIn('id', $this->selectedMediaIds)->get();
    }

    public function render()
    {
        return view('livewire.components.media-picker');
    }
}