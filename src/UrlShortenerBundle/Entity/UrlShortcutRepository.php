<?php

namespace UrlShortenerBundle\Entity;

class UrlShortcutRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy([], ['clicks' => 'DESC']));
    }
}
