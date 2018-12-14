<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\Book;
use AppBundle\Service\FileUploader;

class BookUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        //remove files
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Book) {
            return;
        }
        // загружать только новые файлы
        $screenName = $entity->getScreen();
        if ($screenName instanceof UploadedFile) {
            $screenName = $this->uploader->upload($screenName, $entity->getId());
            $entity->setScreen($screenName);
        }

        $fileName = $entity->getFilePath();
        // загружать только новые файлы
        if ($fileName instanceof UploadedFile) {
            $fileName = $this->uploader->upload($fileName, $entity->getId());
            $entity->setFilePath($fileName);
        }

    }
}