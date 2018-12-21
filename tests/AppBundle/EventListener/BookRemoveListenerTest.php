<?php

namespace Tests\AppBundle\EventListener;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

class BookRemoveListenerTest extends WebTestCase
{
    /**
     * @var string
     */
    private $bookDir;
    /**
     * @var string
     */
    private $bookDirFixture;
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    private $container;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $client = static::createClient();
        $this->container = $client->getContainer();
        $this->bookDir = $this->container->getParameter('books_directory');
        $this->bookDirFixture = $this->container->getParameter('books_fixtures_directory');
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->container->get(FileUploader::class)->emptyDirectory($this->bookDir);
        parent::__construct($name, $data, $dataName);
    }

    public function testCallListenerOnBookDelete()
    {
        $photoName = '1.jpg';
        $fileName = '1.txt';
        //Create a book without files
        $author = $this->em->getRepository(Author::class)->findOneBy([]);
        $book = new Book();
        $book->setReadDate(new \DateTime());
        $book->setTitle('unit test book');
        $book->setAuthor($author);
        $this->em->persist($book);
        $this->em->flush();
        $this->assertNotEmpty($book->getId(), 'Не удалось получить id книги');

        //Set file pathes (with book id)
        $dateForFilePath = date('Y-m');
        $uploadPath = $dateForFilePath . '/' . $book->getId();
        $filePath = $uploadPath . '/' . $fileName;
        $photoPath = $uploadPath . '/' . $photoName;
        $book->setFilePath($filePath);
        $book->setScreen($photoPath);
        $this->em->flush();
        $this->assertEquals($book->getFilePath(), $filePath,'В книге не сохранился корректный путь к файлу');
        $this->assertEquals($book->getScreen(), $photoPath,'В книге не сохранился корректный путь к обложке');

        //Copy files in book folder
        $fileSystem = new Filesystem();
        $fileSystem->copy(
            $this->bookDirFixture . '/' . $fileName,
            $this->bookDir . '/' . $filePath,
            true
        );
        $fileSystem->copy(
            $this->bookDirFixture . '/' . $photoName,
            $this->bookDir . '/' . $photoPath,
            true
        );
        //Проверяем, есть ли файлы в директориях созданной книги
        $fileExists = $fileSystem->exists($this->bookDir . '/' . $filePath);
        $this->assertTrue($fileExists, 'Файл не существует в директории книги');
        $photoExists = $fileSystem->exists($this->bookDir . '/' . $photoPath);
        $this->assertTrue($photoExists, 'Фото не существует в директории книги');

        $this->em->remove($book);
        $this->em->flush();

        //Проверяем, удалились ли файлы в директориях созданной книги
        $fileExists = $fileSystem->exists($this->bookDir . '/' . $filePath);
        $this->assertFalse($fileExists, 'Файл не был удален при удалении книги');
        $photoExists = $fileSystem->exists($this->bookDir . '/' . $photoPath);
        $this->assertFalse($photoExists, 'Фото не было удалено при удалении книги');
    }
}