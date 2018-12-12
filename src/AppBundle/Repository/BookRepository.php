<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Book;

class PostRepository extends EntityRepository
{
    public function findLatest($limit = Book::NUMBER_OF_ITEMS)
    {
        // ...
    }
}