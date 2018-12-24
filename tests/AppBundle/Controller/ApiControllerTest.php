<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    /**
     * @var string
     */
    private $apikey;
    /**
     * @var Author
     */
    private $author;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $client = static::createClient();
        $this->apikey = $client->getContainer()->getParameter('apikey');
        $this->em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $this->author = $this->em->getRepository(Author::class)->findOneBy([]);
        $this->em->createQuery('DELETE AppBundle:Book b')->execute();
        parent::__construct($name, $data, $dataName);
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
        $params = [
            'apikey' => $this->apikey,
            'author' => 1,
            'title' => 'test book scs',
            'allowDownload' => 1,
            'readDate' => '2018-10-10',
        ];
        $client->request('GET', '/api/v1/books/add', $params);
        $response = $client->getResponse();
        $this->assertNotEquals(200, $response->getStatusCode(), 'Добавление книги через GET вернуло неожидаемый тип ошибки');
        $responseContent = $response->getContent();
        $this->assertContains('error', $responseContent, 'Добавление книги через GET не вернуло ошибки в теле запроса');
    }

    public function testAddBookWithSuccessResult()
    {
        $client = static::createClient();
        $paramsVariants = $this->getSuccessParamsVariants($this->apikey, $this->author->getId());

        foreach ($paramsVariants as $message => $params) {
            $client->request('POST', '/api/v1/books/add', $params);
            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(200, $responseCode, $message);

            //Сверяем значения возвращенного объекта книги с теми, что мы передавали в параметрах
            $responseContent = json_decode($response->getContent());
            $this->assertNotEmpty($responseContent->book, 'Не найден результат метода добавления книги');
            /** @var Book $book */
            $book = $client->getContainer()->get('jms_serializer')->deserialize($responseContent->book, Book::class, 'json');
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
        $paramsVariants = $this->getErrorParamsVariants($this->apikey, $this->author->getId());
        foreach ($paramsVariants as $message => $params) {
            $client->request('POST', '/api/v1/books/add', $params);
            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(400, $responseCode, $message);
            $responseContent = $response->getContent();
            $this->assertContains('error', $responseContent);
        }
    }

    public function getSuccessParamsVariants($apikey, $authorId)
    {
        return [
            'Не получилось вставить запись со всеми параметрами' => [
                'apikey' => $apikey,
                'author' => $authorId,
                'title' => 'test book scs',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ],
            'Не получилось вставить запись без allowDownload' => [
                'apikey' => $apikey,
                'author' => $authorId,
                'title' => 'test book scs',
                'readDate' => '2018-10-10',
            ],
        ];
    }

    public function getErrorParamsVariants($apikey, $authorId)
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
                'author' => $authorId,
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
                'author' => $authorId,
                'title' => 'test book',
                'allowDownload' => 1,
            ],
            'Получилось вставить запись с неправильным форматом даты прочтения' => [
                'apikey' => $apikey,
                'author' => $authorId,
                'title' => 'test book',
                'allowDownload' => 1,
                'readDate' => '10-10-2018',
            ],
            'Получилось вставить запись с пустым заголовком' => [
                'apikey' => $apikey,
                'author' => $authorId,
                'title' => '',
                'allowDownload' => 1,
                'readDate' => '2018-10-10',
            ]
        ];
    }

}
