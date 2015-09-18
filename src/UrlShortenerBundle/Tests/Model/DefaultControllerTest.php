<?php

namespace UrlShortenerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/r/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Url to shorten', $crawler->filter('label')->text());
    }

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/r/', ['url' => 'http://ya.ru']);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertContains('Your shortcut', $crawler->filter('h1')->text());
    }
}
