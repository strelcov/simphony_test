<?php
namespace AppBundle\Action;

use AppBundle\Entity\Book;
use AppBundle\Repository\BookRepository;
use AppBundle\Service\FileUploader;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class DeleteBookFile
{
    /**
     * @var FileUploader
     */
    private $uploader;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(FileUploader $uploader, ObjectManager $em)
    {
        $this->uploader = $uploader;
        $this->em = $em;
    }

    /**
     * @param Book $book
     * @throws \Exception
     */
    public function execute(Book $book)
    {
        $dateFolder = $book->getCreatedAt()->format('Y-m');
        $path = $dateFolder . DIRECTORY_SEPARATOR . $book->getId() . DIRECTORY_SEPARATOR . $book->getFilePath();
        $fileIsDel = $this->uploader->deleteFile($path);
        if (!$fileIsDel) {
            throw new \Exception('Не получилось удалить файл');
        }
        $book->setFilePath('');
        $this->em->flush();
        $this->em->getConfiguration()->getResultCacheImpl()->delete(BookRepository::ALL_BOOK_CACHE_KEY);
    }
}