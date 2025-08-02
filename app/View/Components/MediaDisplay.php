<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MediaDisplay extends Component
{
    public $media;
    public $fallbackImage;
    public $containerClass;

    public function __construct($media = null, $fallbackImage = '/images/sih3.png', $containerClass = 'media-container rounded-lg overflow-hidden mb-6 bg-gray-50')
    {
        $this->media = $media;
        $this->fallbackImage = $fallbackImage;
        $this->containerClass = $containerClass;
    }

    public function render()
    {
        return view('components.media-display');
    }
}
