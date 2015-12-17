<?php

namespace UrlShortenerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UrlShortenerBundle\Entity\UrlShortcut;
use UrlShortenerBundle\Form\Type\createUrlShortcutType;
use UrlShortenerBundle\Service\Shortener;

class UrlShortenerController extends Controller
{
    /**
     * @Route("/r/", name="us_add")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(new createUrlShortcutType(), new UrlShortcut());
        $form->handleRequest($request);

        $slug = null;
        if ($form->isValid()) {
            $data = $form->getData();

            $slug = $this->getShortenerService()
                ->registerShortcut($data);
        }

        return [
            'form' => $form->createView(), 
            'slug' => $slug
        ];
    }

    /**
     * @Route("/r/list/", name="us_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $list = $this->getShortenerService()
            ->getList();

        return [
            'list' => $list
        ];
    }

    /**
     * @Route("/r/{slug}", name="us_redirect")
     */
    public function redirectAction(Request $request)
    {
        $slug = $request->get('slug');
        
        if (
            isset($slug) 
            && $this->getShortenerService()
                ->isSlugExists($slug)
        ) {
            $urlShortcut = $this->getShortenerService()
                ->getUrl($slug);
                
            $url = $urlShortcut ? $urlShortcut->getUrl() : null;
            if (!empty($url)) {
                $this->getService()->touch($slug);

                return $this->redirect($url);
            }
        }

        return new Response('Wrong key');
    }

    /**
     * @return Shortener
     */
    private function getShortenerService()
    {
        return $this->get('url_shortener.shortener');
    }

}
