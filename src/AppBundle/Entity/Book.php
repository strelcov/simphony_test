<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="book")
 */
class Book
{
    const NUMBER_OF_ITEMS = 10;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $title;
    /**
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    /**
     * @ORM\Column(type="string")
     */
    private $screen = '';
    /**
     * @ORM\Column(type="string")
     */
    private $file = '';
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $readDate = null;
    /**
     * @ORM\Column(type="boolean")
     */
    private $allowDownload = 0;

    public function __construct()
    {
        $this->readDate = new \DateTime();
    }

}