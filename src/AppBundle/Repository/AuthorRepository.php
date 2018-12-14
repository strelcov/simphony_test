<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Author;

class AuthorRepository extends EntityRepository
{
    public function findByName()
    {

    }
}