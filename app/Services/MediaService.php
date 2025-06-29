<?php
namespace App\Services;

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
