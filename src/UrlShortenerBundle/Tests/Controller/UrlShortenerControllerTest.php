<?php

namespace UrlShortenerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlShortenerControllerTest extends WebTestCase
{
    const TEST_URL = 'http://ya.ru';

    public function testFormExists()
    {
        list($client, $crawler) = $this->visitURL('/r/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Enter Url', $crawler->filter('label')->text());
    }

    public function testCorrectLinkForPostedURL()
    {
        list($client, $crawler) = $this->postData();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains('Your shortcut', $crawler->filter('h2')->text());
        $link = $crawler->selectLink('Visit')->link();

        $client->click($link);
        $client->followRedirect();

        $this->assertEquals(self::TEST_URL, $client->getHistory()->current()->getUri());
    }

    public function testExistsInList()
    {
        $this->postData();
        list($client, $crawler) = $this->visitURL('/r/list/');

        $this->assertContains(self::TEST_URL, $crawler->filter('body')->text());
    }

    /**
     * @return array
     */
    private function postData()
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/r/', [
            'create_url_shortcut' => [ 'url' => self::TEST_URL]
        ]);
        $form = $crawler->filter('form')->form();
        $crawler = $client->submit($form);

        return [$client, $crawler];
    }

    /**
     * @param string $url
     * @return array
     */
    private function visitURL($url)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $url);

        return [$client, $crawler];
    }
}
