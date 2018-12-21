<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookControllerTest extends WebTestCase
{
    private $userLogin;
    private $userPassword;
    private $bookDir;
    private $bookDirFixture;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $client = static::createClient();
        $this->bookDir = $client->getContainer()->getParameter('books_directory');
        $this->bookDirFixture = $client->getContainer()->getParameter('books_fixtures_directory');
        $this->userLogin = $client->getContainer()->getParameter('test_db_user_name');
        $this->userPassword = $client->getContainer()->getParameter('test_db_user_password');
        $this->em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $this->fileUploader = $client->getContainer()->get(FileUploader::class);
        $this->em->createQuery('DELETE AppBundle:Book b')->execute();
        parent::__construct($name, $data, $dataName);
    }

    private function clearBookDir()
    {
        $this->fileUploader->emptyDirectory($this->bookDir);
    }

    private function scsAuth(Client $client)
    {
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Войти')->form([
            '_username'  => $this->userLogin,
            '_password'  => $this->userPassword,
        ]);
        $client->submit($form);
        return $client->followRedirect();
    }

    public function testAuthWithError()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/book/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertContains('/book/new', $crawler->getUri());
        $crawler = $client->followRedirect();
        $this->assertContains('/login', $crawler->getUri());

        $variants = [
            'Авторизация с неправильным логином и паролем' => [
                '_username'  => 'wrongtestuser',
                '_password'  => 'wrongtestpassword',
            ],
            'Авторизация с неправильным логином' => [
                '_username'  => 'wrongtestuser',
                '_password'  => $this->userPassword,
            ],
            'Авторизация с неправильным паролем' => [
                '_username'  => $this->userLogin,
                '_password'  => 'wrongtestpassword',
            ],
        ];
        foreach ($variants as $message => $variant) {
            $form = $crawler->selectButton('Войти')->form($variant);
            $client->submit($form);
            $crawler = $client->followRedirect();
            $this->assertContains('/login', $crawler->getUri(), $message);
        }
    }

    public function testSuccessAuth()
    {
        $client = static::createClient();
        $crawler = $this->scsAuth($client);
        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Вы авторизованы как")')->count(),
            'Неудачная попытка авторизации'
        );
    }

    public function testAddBook()
    {
        $client = static::createClient();
        $this->scsAuth($client);
        $cases = $this->getSuccessParamsVariants('appbundle_book');
        foreach ($cases as $message => $params) {
            //В цикле прогнать по успешным и неуспешным кейсам
            $crawler = $client->request('GET', '/book/new');
            $form = $crawler->selectButton('Сохранить')->form($params);
            $client->submit($form);
            $crawler = $client->followRedirect();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertNotContains('/book/new', $crawler->getUri());
            $this->assertGreaterThan(
                0,
                $crawler->filter('html:contains("Список книг")')->count(),
                $message
            );
        }
        $this->clearBookDir();
    }

    public function getSuccessParamsVariants($formName)
    {
        $photo = new UploadedFile(
            $this->bookDirFixture . '/1.jpg',
            '1.jpg',
            'image/jpeg'
        );
        $file = new UploadedFile(
            $this->bookDirFixture . '/1.txt',
            '1.txt'
        );
        return [
            'Не получилось вставить запись со всеми параметрами' => [
                "{$formName}[title]" => 'книга 1',
                "{$formName}[allowDownload]" => 1,
                "{$formName}[readDate]" => '2018-12-12',
                "{$formName}[screen]" => $photo,
                "{$formName}[filePath]" => $file,
            ],
            'Не получилось вставить запись без allowDownload' => [
                "{$formName}[title]" => 'книга 2',
                "{$formName}[readDate]" => '2018-12-12',
                "{$formName}[screen]" => $photo,
                "{$formName}[filePath]" => $file,
            ],
            'Не получилось вставить запись без фото' => [
                "{$formName}[title]" => 'книга 3',
                "{$formName}[allowDownload]" => 1,
                "{$formName}[readDate]" => '2018-12-12',
                "{$formName}[filePath]" => $file,
            ],
            'Не получилось вставить запись без файла книги' => [
                "{$formName}[title]" => 'книга 4',
                "{$formName}[allowDownload]" => 1,
                "{$formName}[readDate]" => '2018-12-12',
                "{$formName}[screen]" => $photo,
            ],
            'Не получилось вставить запись без файла книги и фото' => [
                "{$formName}[title]" => 'книга 5',
                "{$formName}[allowDownload]" => 1,
                "{$formName}[readDate]" => '2018-12-12',
            ],
        ];
    }

    public function getErrorParamsVariants($formName)
    {
        return [
            'Получилось вставить запись без заголовка' => [
                "{$formName}[allowDownload]" => 1,
                "{$formName}[readDate]" => '2018-12-12',
            ],
            'Получилось вставить запись без даты прочтения' => [
                "{$formName}[title]" => 'книга 6',
                "{$formName}[allowDownload]" => 0,
            ],
        ];
    }

}
