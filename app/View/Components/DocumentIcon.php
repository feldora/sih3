<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DocumentIcon extends Component
{
    public $extension;
    public $size;
    public $class;

    public $fullSize;

    /**
     * Create a new component instance.
     *
     * @param string $extension File extension
     * @param string $size Icon size (sm, md, lg, xl) or 'full' for container-filling
     * @param string $class Additional CSS classes
     * @param bool $fullSize Whether to make icon fill container completely
     */
    public function __construct($extension, $size = 'md', $class = '', $fullSize = false)
    {
        $this->extension = strtolower($extension);
        $this->size = $size;
        $this->class = $class;
        $this->fullSize = $fullSize || $size === 'full';
    }

    /**
     * Get the icon SVG based on file extension
     */
    public function getIconSvg()
    {
        $sizeMap = [
            'xs' => 'w-3 h-3',
            'sm' => 'w-4 h-4',
            'md' => 'w-5 h-5',
            'lg' => 'w-6 h-6',
            'xl' => 'w-8 h-8',
            '2xl' => 'w-10 h-10'
        ];

        // If fullSize is true or size is 'full', make it fill the container
        if ($this->fullSize || $this->size === 'full') {
            $sizeClass = 'w-full h-full';
        } else {
            $sizeClass = $sizeMap[$this->size] ?? $sizeMap['md'];
        }
        
        $baseClass = "{$sizeClass} {$this->class}";

        switch ($this->extension) {
            case 'pdf':
                return $this->getPdfIcon($baseClass);
            
            case 'doc':
            case 'docx':
                return $this->getWordIcon($baseClass);
            
            case 'xls':
            case 'xlsx':
                return $this->getExcelIcon($baseClass);
            
            case 'ppt':
            case 'pptx':
                return $this->getPowerpointIcon($baseClass);
            
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
            case 'webp':
                return $this->getImageIcon($baseClass);
            
            case 'txt':
                return $this->getTextIcon($baseClass);
            
            case 'zip':
            case 'rar':
            case '7z':
                return $this->getArchiveIcon($baseClass);
            
            case 'mp4':
            case 'avi':
            case 'mov':
            case 'wmv':
            case 'flv':
                return $this->getVideoIcon($baseClass);
            
            case 'mp3':
            case 'wav':
            case 'flac':
            case 'aac':
                return $this->getAudioIcon($baseClass);
            
            default:
                return $this->getDefaultIcon($baseClass);
        }
    }

    /**
     * PDF Icon
     */
    private function getPdfIcon($class)
    {
        return '<svg class="' . $class . ' text-red-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M10.5,12.5C10.84,12.5 11.13,12.61 11.35,12.82C11.57,13.03 11.69,13.32 11.69,13.69C11.69,14.06 11.57,14.35 11.35,14.56C11.13,14.77 10.84,14.88 10.5,14.88H9.81V16H9V12H10.5M9.81,14.13H10.5C10.67,14.13 10.8,14.08 10.89,13.97C10.97,13.86 11,13.69 11,13.5C11,13.31 10.97,13.14 10.89,13.03C10.8,12.92 10.67,12.87 10.5,12.87H9.81V14.13M12.19,12H13.5C14,12 14.4,12.18 14.68,12.53C14.97,12.88 15.11,13.36 15.11,13.97C15.11,14.58 14.97,15.06 14.68,15.41C14.4,15.76 14,15.94 13.5,15.94H13V16H12.19V12M13,15.19H13.5C13.75,15.19 13.93,15.1 14.05,14.91C14.16,14.72 14.22,14.42 14.22,14C14.22,13.58 14.16,13.28 14.05,13.09C13.93,12.9 13.75,12.81 13.5,12.81H13V15.19M16.81,12H18.69V12.75H17.63V14H18.56V14.75H17.63V16H16.81V12Z" />
        </svg>';
    }

    /**
     * Word Icon
     */
    private function getWordIcon($class)
    {
        return '<svg class="' . $class . ' text-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M7,13L8.5,17.5L9.75,13.75L11,17.5L12.5,13H14L11.5,19H10L8.75,15.5L7.5,19H6L3.5,13H5L6.5,17.5L7,13Z" />
        </svg>';
    }

    /**
     * Excel Icon
     */
    private function getExcelIcon($class)
    {
        return '<svg class="' . $class . ' text-green-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M12,13L10,11L8,13L10,15L12,13M16,11H14V13H16V11M16,15H14V17H16V15M12,15L10,17L8,15L10,13L12,15Z" />
        </svg>';
    }

    /**
     * PowerPoint Icon
     */
    private function getPowerpointIcon($class)
    {
        return '<svg class="' . $class . ' text-orange-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M9,12H10.5C11,12 11.4,12.18 11.68,12.53C11.97,12.88 12.11,13.36 12.11,13.97C12.11,14.58 11.97,15.06 11.68,15.41C11.4,15.76 11,15.94 10.5,15.94H10V17H9V12M10,15.19H10.5C10.75,15.19 10.93,15.1 11.05,14.91C11.16,14.72 11.22,14.42 11.22,14C11.22,13.58 11.16,13.28 11.05,13.09C10.93,12.9 10.75,12.81 10.5,12.81H10V15.19Z" />
        </svg>';
    }

    /**
     * Image Icon
     */
    private function getImageIcon($class)
    {
        return '<svg class="' . $class . ' text-purple-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z" />
        </svg>';
    }

    /**
     * Text Icon
     */
    private function getTextIcon($class)
    {
        return '<svg class="' . $class . ' text-gray-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
            <path d="M7,13V15H17V13H7M7,9V11H17V9H7M7,17V19H14V17H7Z" />
        </svg>';
    }

    /**
     * Archive Icon
     */
    private function getArchiveIcon($class)
    {
        return '<svg class="' . $class . ' text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,12H19V13H14V12M14,16H19V17H14V16M11,12H12V16H11V12M6,12H10V13H6V12M6,16H10V17H6V16M15,2H8.5L7,3.5V5H17V3.5L15,2M19,7H5V21H19V7Z" />
        </svg>';
    }

    /**
     * Video Icon
     */
    private function getVideoIcon($class)
    {
        return '<svg class="' . $class . ' text-pink-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17,10.5V7A1,1 0 0,0 16,6H4A1,1 0 0,0 3,7V17A1,1 0 0,0 4,18H16A1,1 0 0,0 17,17V13.5L21,17.5V6.5L17,10.5Z" />
        </svg>';
    }

    /**
     * Audio Icon
     */
    private function getAudioIcon($class)
    {
        return '<svg class="' . $class . ' text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,3.23V5.29C16.89,6.15 19,8.83 19,12C19,15.17 16.89,17.84 14,18.7V20.77C18,19.86 21,16.28 21,12C21,7.72 18,4.14 14,3.23M16.5,12C16.5,10.23 15.5,8.71 14,7.97V16C15.5,15.29 16.5,13.76 16.5,12M3,9V15H7L12,20V4L7,9H3Z" />
        </svg>';
    }

    /**
     * Default Icon
     */
    private function getDefaultIcon($class)
    {
        return '<svg class="' . $class . ' text-gray-500" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
        </svg>';
    }

    /**
     * Get the view / contents of the component.
     */
    public function render()
    {
        return function (array $data) {
            return $this->getIconSvg();
        };
    }
}