<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function findLatest()
    {
        return $this->findBy([], ['read_date' => 'ASC']);
    }
}