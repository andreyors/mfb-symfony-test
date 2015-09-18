<?php

namespace UrlShortenerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use UrlShortenerBundle\Entity\UrlShortcut;
use UrlShortenerBundle\Form\Type\createUrlShortcutType;

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
        if ($form->isValid()) {
            $data = $form->getData();

            $slug = $this->getService()->add($data);

            if (!empty($slug)) {
                return $this->redirect('/r/view/' . $slug);
            }
        }

        return $this->render('UrlShortenerBundle:UrlShortener:index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/r/view/{slug}", name="us_view")
     * @Template()
     */
    public function viewAction(Request $request)
    {
        $slug = $request->get('slug');
        if (!$this->getService()->isSlugExists($slug)) {
            die('Wrong Key');
        }

        return $this->render('UrlShortenerBundle:UrlShortener:view.html.twig', ['slug' => $slug]);
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
        if (!empty($slug)) {
            $urlShortcut = $this->getService()->getUrl($slug);
            $url = $urlShortcut ? $urlShortcut->getUrl() : null;
            if (!empty($url)) {
                $this->getService()->touch($slug);

                return $this->redirect($url);
            }
        }

        die('Wrong Key');
    }

    /**
     * @return object
     */
    private function getService()
    {
        return $this->get('url_shortener.shortener');
    }


}