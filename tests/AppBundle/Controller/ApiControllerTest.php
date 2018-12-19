<?php

namespace Tests\AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{

    /**
     * TODO: мб можно сделать через dataProvider
     */
    public function testAuthError()
    {
        $client = static::createClient();
        $urls = [
            '/api/v1/books/add',
            '/api/v1/books/add?apikey=1112121121',
        ];
        foreach ($urls as $url) {
            $client->request('GET', $url);
            $response = $client->getResponse();
            $responseContent = $response->getContent();
            $this->assertContains('error', $responseContent);
            $this->assertContains('api key', $responseContent);
            $responseCode = $response->getStatusCode();
            $this->assertEquals(401, $responseCode);
        }
    }

    public function testAddBookByApi()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/books/add');
        $response = $client->getResponse();
        $responseContent = $response->getContent();
        $this->assertContains('error', $responseContent);
        $this->assertContains('api key', $responseContent);
        $responseCode = $response->getStatusCode();
        $this->assertEquals(401, $responseCode);
    }

    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/book/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /book/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'appbundle_book[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'appbundle_book[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    */
}
