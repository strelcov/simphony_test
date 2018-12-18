<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Accessor;

/**
 * @ORM\Entity
 * @ORM\Table(name="book")
 * @ExclusionPolicy("all")
 */
class Book
{
    const NUMBER_OF_ITEMS = 10;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Expose
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Expose
     */
    private $title;
    /**
     * @ORM\ManyToOne(targetEntity="Author")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * @Assert\NotBlank
     * @Expose
     */
    private $author;
    /**
     * @ORM\Column(type="string", options={"default":""})
     * @Expose
     * @Accessor(getter="getScreenLink")
     */
    private $screen = '';
    /**
     * @ORM\Column(type="string", options={"default":""})
     * @Expose
     * @Accessor(getter="getFileLink")
     */
    private $filePath = '';
    /**
     * @ORM\Column(type="date", nullable=true, options={"default":NULL})
     * @Assert\Date
     */
    private $readDate = null;
    /**
     * @ORM\Column(type="boolean")
     * @Expose
     */
    private $allowDownload = false;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * @return mixed
     */
    public function getAllowDownload()
    {
        return $this->allowDownload;
    }

    /**
     * @return mixed
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return mixed
     */
    public function getReadDate()
    {
        return $this->readDate;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param mixed $screen
     */
    public function setScreen($screen)
    {
        $this->screen = $screen;
    }

    /**
     * @param mixed $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param mixed $readDate
     */
    public function setReadDate($readDate)
    {
        $this->readDate = $readDate;
    }

    /**
     * @param mixed $allowDownload
     */
    public function setAllowDownload($allowDownload)
    {
        $this->allowDownload = $allowDownload;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getScreenLink()
    {
        if (empty($this->screen)) {
            return null;
        }
        return $this->screen;
    }

    /**
     * @return mixed
     */
    public function getFileLink()
    {
        if (empty($this->filePath) || !$this->getAllowDownload()) {
            return null;
        }
        return $this->filePath;
    }


}