<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    private  $path;

    public function __construct($path = '%books_public_directory%')
    {
        $this->path = $path;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('resize_image', [$this, 'resizeImage'], ['is_safe' => ['html']]),
        ];
    }

    public function resizeImage($img, $maxWidth, $maxHeight)
    {
        if (empty($img)) {
            return '';
        }
        $img = $this->path . '/' . $img;
        return '<img src="' . $img . '" style="max-width: ' . $maxWidth . '; max-height: ' . $maxHeight . '">';
    }
}