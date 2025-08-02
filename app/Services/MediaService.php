<?php
namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Model;

class MediaService
{
    /**
     * Attach media to a model.
     *
     * @param Model $model
     * @param mixed $mediaFile
     * @param string $collection
     * @param string $disk
     * @param bool $clearOld
     * @return void
     */
    public function attachMedia(Model $model, $mediaFile, $collection = 'default', $disk = 'media', $clearOld = false)
    {
        if ($clearOld) {
            $model->clearMediaCollection($collection);
        }
        \Log::info("Saving media to disk [$disk] in collection [$collection]");
        \Log::info("Disk media path: " . Storage::disk('media')->path(''));
        $model->addMedia($mediaFile)->toMediaCollection($collection, $disk);
    }

    /**
     * Remove all media from a collection.
     *
     * @param Model $model
     * @param string $collection
     * @return void
     */
    public function clearMedia(Model $model, $collection = 'default')
    {
        $model->clearMediaCollection($collection);
    }
}
