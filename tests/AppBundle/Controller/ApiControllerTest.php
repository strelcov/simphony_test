<?php

namespace Tests\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{

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

    public function testAddBookByApi()
    {
        $client = static::createClient();
        $apikey = $client->getKernel()->getContainer()->getParameter('apikey');
        //TODO: реструктурировать массив, чтобы можно было пройти по всем успешным и не успешным
        $paramsVariants = [
            [
                'Не получилось вставить запись со всеми параметрами' => [
                    'success' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'title' => 'test book scs',
                        'allowDownload' => 1,
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],
            [
                'Не получилось вставить запись без allowDownload' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'title' => 'test book scs',
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],
            [
                'Получилось вставить запись с несуществующим автором' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => -1,
                        'title' => 'test book',
                        'allowDownload' => 1,
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],
            [
                'Получилось вставить запись без названия книги' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'allowDownload' => 1,
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],
            [
                'Получилось вставить запись без автора' => [
                    'error' => [
                        'apikey' => $apikey,
                        'title' => 'test book',
                        'allowDownload' => 1,
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],
            [
                'Получилось вставить запись без даты прочтения' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'title' => 'test book',
                        'allowDownload' => 1,
                    ]
                ]
            ],
            [
                'Получилось вставить запись с неправильным форматом даты прочтения' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'title' => 'test book',
                        'allowDownload' => 1,
                        'readDate' => 10-10-2018,
                    ]
                ]
            ],
            [
                'Получилось вставить запись с пустым заголовком' => [
                    'error' => [
                        'apikey' => $apikey,
                        'author' => 1,
                        'title' => '',
                        'allowDownload' => 1,
                        'readDate' => 2018-10-10,
                    ]
                ]
            ],

        ];
        foreach ($paramsVariants as $paramsVariant) {
            //TODO: разделение на правильные и неправильные кейсы
            $client->request('GET', '/api/v1/books/add', $params);
            $response = $client->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
            $responseContent = $response->getContent();
            $this->assertContains('error', $responseContent);
            $this->assertContains('must use method post', $responseContent);

            $client->request('POST', '/api/v1/books/add', $params);

            $response = $client->getResponse();
            $responseCode = $response->getStatusCode();
            $this->assertEquals(200, $responseCode);

            $responseContent = $response->getContent();
            //десериализуем возвращаемый объект и сравниваем его поля с параметрами, кототрые передали в него

            //$this->assertContains('error', $responseContent);
            //$this->assertContains('api key', $responseContent);
        }

    }

}
