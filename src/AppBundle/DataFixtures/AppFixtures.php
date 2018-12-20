<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function createUser()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername($this->container->getParameter('test_db_user_name'));
        $user->setEmail($this->container->getParameter('test_db_user_email'));
        $user->setPlainPassword($this->container->getParameter('test_db_user_password'));
        $user->setRoles(['ROLE_USER']);
        $userManager->updateUser($user, true);
    }

    private function createAuthors(ObjectManager $manager)
    {
        $author = new Author('Тургенев');
        $author2 = new Author('Пушкин');
        $author3 = new Author('Марк Твен');
        $manager->persist($author);
        $manager->persist($author2);
        $manager->persist($author3);
        $manager->flush();
    }

    public function load(ObjectManager $manager)
    {
        $this->createUser();
        $this->createAuthors($manager);
    }
}