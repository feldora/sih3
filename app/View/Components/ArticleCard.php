<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ArticleCard extends Component
{
    public $title;
    public $image;
    public $date;
    public $month;
    public $url;
    public $timeAgo;
    public $views;
    public $uploader;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $title,
        $image,
        $date,
        $month,
        $url,
        $timeAgo,
        $views,
        $uploader
    ) {
        $this->title = $title;
        $this->image = $image;
        $this->date = $date;
        $this->month = $month;
        $this->url = $url;
        $this->timeAgo = $timeAgo;
        $this->views = $views;
        $this->uploader = $uploader;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.post-card');
    }
}
