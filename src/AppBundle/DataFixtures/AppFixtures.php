<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $author = new Author('Тургенев');
        $author2 = new Author('Пушкин');
        $author3 = new Author('Марк Твен');
        $manager->persist($author);
        $manager->persist($author2);
        $manager->persist($author3);
        $manager->flush();
    }
}