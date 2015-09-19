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
        $urlShortcut = new UrlShortcut();
        $form = $this->createForm(new createUrlShortcutType(), $urlShortcut);

        $form->handleRequest($request);

        $slug = "";
        if ($form->isValid()) {
            $data = $form->getData();

            $slug = $this->getService()->registerShortcut($data);
        }

        return $this->render('UrlShortenerBundle:UrlShortener:index.html.twig', ['form' => $form->createView(), 'slug' => $slug]);
    }

    /**
     * @Route("/r/list/", name="us_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $list = $this->getService()->getList();

        return $this->render('UrlShortenerBundle:UrlShortener:list.html.twig', ['list' => $list]);
    }

    /**
     * @Route("/r/{slug}", name="us_redirect")
     */
    public function redirectAction(Request $request)
    {
        $slug = $request->get('slug');
        if (isset($slug) && $this->getService()->isSlugExists($slug)) {
            $urlShortcut = $this->getService()->getUrl($slug);
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
    private function getService()
    {
        return $this->get('url_shortener.shortener');
    }


}