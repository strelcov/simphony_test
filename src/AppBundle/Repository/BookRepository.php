<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Book;

class BookRepository extends EntityRepository
{
    public function findLatest($limit = Book::NUMBER_OF_ITEMS)
    {
        return $this->findBy([], ['read_date' => 'ASC']);
    }
}