<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookRepository
{
    const ALL_BOOK_CACHE_KEY = 'all_books';

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    private $queryBuilder;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->queryBuilder = $entityManager->createQueryBuilder();
    }

    public function findAllWithReadSort()
    {
        $bookList = $this->queryBuilder
            ->select('x')
            ->from(Book::class, 'x')
            ->join('x.author', 'author')
            ->orderBy('x.readDate')
            ->getQuery()
            ->useResultCache(true, 3600 * 24, self::ALL_BOOK_CACHE_KEY)
            ->getResult();

        return $bookList;
    }
}