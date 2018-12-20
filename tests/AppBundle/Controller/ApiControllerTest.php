<?php

namespace Tests\AppBundle\Tests\Controller;

use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class ApiControllerTest extends WebTestCase
{
    protected static $application;

    protected function setUp()
    {
        //$this->runCommand('doctrine:database:create');
        //$this->runCommand('doctrine:schema:update --force');
        //$this->runCommand('doctrine:fixtures:load --n');
    }

    protected function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();
            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    private function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return $this->getApplication()->run(new StringInput($command));
    }

    public function testAuthError()
    {
        $client = static::createClient();
        $variants = [
            ['url' => '/api/v1/books/add', 'params' => []],
            ['url' => '/api/v1/books/add', 'params' => ['apikey' => '1112121121']],
        ];
        foreach ($variants as $variant) {
            $client->request('POST', $variant['url'], $variant['params']);
            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(401, $responseCode);
            $responseContent = $response->getContent();
            $this->assertContains('error', $responseContent, 'Не найдено упоминание об ошибке в теле ответа');
            $this->assertContains('api key', $responseContent, 'Не найдено упоминание об api key в теле ответа');
        }
    }

    public function testAddBookByGetMethod()
    {
        $client = static::createClient();
        $apikey = $client->getKernel()->getContainer()->getParameter('apikey');
        $params = [
            'apikey' => $apikey,
            'author' => 1,
            'title' => 'test book scs',
            'allowDownload' => 1,
            'readDate' => '2018-10-10',
        ];
        $client->request('GET', '/api/v1/books/add', $params);
        $response = $client->getResponse();
        $this->assertEquals(400, $response->getStatusCode());
        $responseContent = $response->getContent();
        $this->assertContains('error', $responseContent);
        $this->assertContains('must use method post', $responseContent);
    }

    public function testAddBookWithSuccessResult()
    {
        $client = static::createClient();
        $apikey = $client->getKernel()->getContainer()->getParameter('apikey');
        $paramsVariants = $this->getSuccessParamsVariants($apikey);
        foreach ($paramsVariants as $message => $params) {
            $client->request('POST', '/api/v1/books/add', $params);
            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(200, $responseCode, $message);

            //Сверяем значения возвращенного объекта книги с теми, что мы передавали в параметрах
            $responseContent = json_decode($response->getContent());
            $this->assertNotEmpty($responseContent->book, 'Не найден результат метода добавления книги');
            /** @var Book $book */
            $book = $client->getKernel()->getContainer()->get('jms_serializer')->deserialize($responseContent->book, Book::class, 'json');
            $this->assertEquals($params['title'], $book->getTitle(), 'Вставлен заголовок книги, который не ожидался');
            if (!empty($params['allowDownload'])) {
                $this->assertEquals((bool)$params['allowDownload'], $book->getAllowDownload(), 'Вставлен параметр allowDownload, который не ожидался');
            } else {
                $this->assertFalse($book->getAllowDownload(), 'Вставлен параметр allowDownload, который не ожидался');
            }
            $this->assertEquals($params['readDate'], $book->getReadDate()->format('Y-m-d'), 'Вставлен параметр readDate, который не ожидался');
            $this->assertEquals($params['author'], $book->getAuthor()->getId(), 'Вставлен id автора не тот, который не ожидался');
        }
    }

    public function testAddBookWithErrorResult()
    {
        $client = static::createClient();
        $apikey = $client->getKernel()->getContainer()->getParameter('apikey');
        $paramsVariants = $this->getErrorParamsVariants($apikey);
        foreach ($paramsVariants as $message => $params) {
            $client->request('POST', '/api/v1/books/add', $params);
            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(400, $responseCode, $message);
            $responseContent = $response->getContent();
            $this->assertContains('error', $responseContent);
        }
    }

    public function getSuccessParamsVariants($apikey)
    {
        return [
            'Не получилось вставить запись со всеми параметрами' => [
                'apikey' => $apikey,
                'author' => 1,
                'title' => 'test book scs',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ],
            'Не получилось вставить запись без allowDownload' => [
                'apikey' => $apikey,
                'author' => 1,
                'title' => 'test book scs',
                'readDate' => '2018-10-10',
            ],
        ];
    }

    public function getErrorParamsVariants($apikey)
    {
        return [
            'Получилось вставить запись с несуществующим автором' => [
                'apikey' => $apikey,
                'author' => -1,
                'title' => 'test book',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ],
            'Получилось вставить запись без названия книги' => [
                'apikey' => $apikey,
                'author' => 1,
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ],
            'Получилось вставить запись без автора' => [
                'apikey' => $apikey,
                'title' => 'test book',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ],
            'Получилось вставить запись без даты прочтения' => [
                'apikey' => $apikey,
                'author' => 1,
                'title' => 'test book',
                'allowDownload' => 1,
            ],
            'Получилось вставить запись с неправильным форматом даты прочтения' => [
                'apikey' => $apikey,
                'author' => 1,
                'title' => 'test book',
                'allowDownload' => 1,
                'readDate' => '10-10-2018',
            ],
            'Получилось вставить запись с пустым заголовком' => [
                'apikey' => $apikey,
                'author' => 1,
                'title' => '',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ]
        ];
    }

}
