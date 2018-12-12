<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

class Book
{
    const NUMBER_OF_ITEMS = 10;

    private $id;
    private $title;
    private $author;
    private $screen;
    private $file;
    private $readDate;
    private $allowDownload;

    // ...
}