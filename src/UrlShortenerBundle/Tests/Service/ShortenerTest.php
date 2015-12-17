<?php

namespace UrlShortenerBundle\Tests\Service;

use UrlShortenerBundle\Entity\UrlShortcut;
use UrlShortenerBundle\Service\Shortener;
use \PHPUnit_Framework_TestCase as TestCase;

class ShortenerTest extends TestCase
{
    public function setUp()
    {
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this
            ->getMockBuilder('Doctrine\Common\Persistence\ObjectRepository')
            ->setMethods(['find', 'findAll', 'findBy', 'findOneBy', 'getClassName', 'findOneByUrl'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->om->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->shortener = new Shortener($this->om);
    }

    public function testShouldReturnCorrectDataForSlugFunctions()
    {
        $shortener = $this->shortener;

        $slug = $shortener->id2slug(31337);

        $expected = "o6h";
        $this->assertEquals($expected, $slug);

        $id = $shortener->slug2id($slug);

        $expected = "31337";
        $this->assertEquals($expected, $id);
    }

    public function testShouldRegisterAShortcut()
    {
        $us = new UrlShortcut();
        $us->setSlug('zloi');
        $us->setUrl('http://ya.ru');

        $this->repository->expects($this->any())
            ->method('findOneByUrl')
            ->with($this->equalTo('http://ya.ru'))
            ->will($this->returnValue($us));

        $shortener = $this->shortener;

        $urlShortcut = new UrlShortcut();
        $urlShortcut->setUrl('http://ya.ru');

        $slug = $shortener->registerShortcut($urlShortcut);

        $expected = 'zloi';
        $this->assertEquals($expected, $slug);
    }

    public function testShouldReturnNotExists()
    {
        $this->repository->expects($this->once())
            ->method('findOneByUrl')
            ->with($this->equalTo('http://yandex.kz'))
            ->will($this->returnValue(false));

        $shortener = $this->shortener;

        $actual = $shortener->isExists('http://yandex.kz');

        $expected = false;
        $this->assertEquals($expected, $actual);
    }

    public function testShouldReturnExists()
    {
        $us = new UrlShortcut();
        $us->setSlug('zloi');
        $us->setUrl('http://ya.ru');

        $this->repository->expects($this->once())
            ->method('findOneByUrl')
            ->with($this->equalTo('http://ya.ru'))
            ->will($this->returnValue($us));

        $shortener = $this->shortener;

        $actual = $shortener->isExists('http://ya.ru');

        $this->assertEquals(true, $actual);
    }

}
