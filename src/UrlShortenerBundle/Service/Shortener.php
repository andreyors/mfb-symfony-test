<?php

namespace UrlShortenerBundle\Service;

use UrlShortenerBundle\Entity\UrlShortcut;

class Shortener
{
    /**
     * @var object
     */
    protected $doctrine;

    const REPO_NAME = 'UrlShortenerBundle:UrlShortcut';

    /**
     * @param object $doctrine
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isExists($url)
    {
        $result = $this->getByUrl($url);

        return !empty($result);
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function isSlugExists($slug)
    {
        $result = $this->getUrl($slug);

        return !empty($result);
    }

    /**
     * @return object
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @param string $slug
     */
    public function touch($slug)
    {
        $urlShortcut = $this->getUrl($slug);
        $urlShortcut->setClicks($urlShortcut->getClicks() + 1);

        $this->save($urlShortcut);
    }

    /**
     * @param UrlShortcut $urlShortcut
     * @return string
     */
    public function add(UrlShortcut $urlShortcut)
    {
        $url = strtolower(trim($urlShortcut->getUrl()));
        if (!$this->isExists($url)) {
            $urlShortcut->setSlug('dummy');
            $urlShortcut->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
            $urlShortcut->setClicks(0);

            $urlShortcut = $this->save($urlShortcut, false);

            $id = $urlShortcut->getId();

            $urlShortcut->setSlug($this->id2slug($id));
            $urlShortcut = $this->save($urlShortcut);
        } else {
            $urlShortcut = $this->getByUrl($url);
        }

        return $urlShortcut->getSlug();
    }

    /**
     * @param string $url
     * @return object
     */
    public function getByUrl($url)
    {
        return $this->getDoctrine()
            ->getRepository(self::REPO_NAME)
            ->findOneByUrl($url);
    }

    /**
     * @param string $slug
     * @return string
     */
    public function getUrl($slug)
    {
        return $this->getDoctrine()
            ->getRepository(self::REPO_NAME)
            ->findOneBySlug($slug);
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->getDoctrine()
            ->getRepository(self::REPO_NAME)
            ->findAll();
    }

    /**
     * @param string $slug
     * @return int
     */
    public function slug2id($slug)
    {
        return base_convert($slug, 36, 10);
    }

    /**
     * @param int $number
     * @return string
     */
    public function id2slug($number)
    {
        return base_convert($number, 10, 36);
    }

    /**
     * @param UrlShortcut $urlShortcut
     * @param bool|false $flush
     * @return UrlShortcut
     */
    private function save(UrlShortcut $urlShortcut, $flush = true)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($urlShortcut);
        if ($flush) {
            $em->flush();
        }

        return $urlShortcut;
    }

}