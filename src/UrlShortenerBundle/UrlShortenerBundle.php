<?php

namespace UrlShortenerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use UrlShortenerBundle\DependencyInjection\UrlShortenerExtension;

class UrlShortenerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new UrlShortenerExtension();
    }
}
