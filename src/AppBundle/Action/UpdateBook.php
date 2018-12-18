<?php
namespace AppBundle\Action;

use AppBundle\Entity\Book;
use AppBundle\Helper\BookFileSaver;
use AppBundle\Repository\BookRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class UpdateBook
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var BookFileSaver
     */
    private $bookFileSaver;

    public function __construct(ObjectManager $em, BookFileSaver $bookFileSaver)
    {
        $this->em = $em;
        $this->bookFileSaver = $bookFileSaver;
    }

    /**
     * @param Book $book
     * @throws \Exception
     */
    public function execute(Book $book, $form)
    {
        $this->bookFileSaver->saveFiles($book, $form);
        $this->em->flush();
        $this->em->getConfiguration()->getResultCacheImpl()->delete(BookRepository::ALL_BOOK_CACHE_KEY);
    }
}