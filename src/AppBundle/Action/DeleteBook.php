<?php
namespace AppBundle\Action;

use AppBundle\Entity\Book;
use AppBundle\Repository\BookRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class DeleteBook
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Book $book
     * @throws \Exception
     */
    public function execute(Book $book)
    {
        $this->em->remove($book);
        $this->em->flush();
        $this->em->getConfiguration()->getResultCacheImpl()->delete(BookRepository::ALL_BOOK_CACHE_KEY);
    }
}