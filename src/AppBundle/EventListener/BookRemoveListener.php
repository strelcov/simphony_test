<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Book;
use AppBundle\Service\FileUploader;

class BookRemoveListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Book) {
            return;
        }
        $dateFolder = $entity->getCreatedAt()->format('Y-m');
        $folderPath = $this->uploader->getTargetDir()
            . DIRECTORY_SEPARATOR . $dateFolder
            . DIRECTORY_SEPARATOR . $entity->getId();
        $this->uploader->emptyDirectory($folderPath, true);
    }
}