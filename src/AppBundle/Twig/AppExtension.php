<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private  $path;

    public function __construct($path = '%books_public_directory%')
    {
        $this->path = $path;
    }

    /**
     * @return array|\Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('resize_image', [$this, 'resizeImage'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $img
     * @param $maxWidth
     * @param $maxHeight
     * @return string
     */
    public function resizeImage($img, $maxWidth, $maxHeight)
    {
        if (empty($img)) {
            return '';
        }
        $img = $this->path . '/' . $img;
        return '<img src="' . $img . '" style="max-width: ' . $maxWidth . '; max-height: ' . $maxHeight . '">';
    }
}