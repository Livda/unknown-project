<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * The real route link for "/" is enforced in the file config/routes.yaml
     *
     * @Route("/", name="homepage", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route({
     *      "en": "/term-and-conditions-of-use",
     *      "fr": "/condition-generales-d'utilisation"
     *  },
     *  name="term_condition_use",
     *  methods={"GET", "POST"}
     * )
     */
    public function gcu(): Response
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route({
     *      "en": "/term-and-conditions-of-sales",
     *      "fr": "/condition-generales-de-vente"
     *  },
     *  name="term_condition_sales",
     *  methods={"GET", "POST"}
     * )
     */
    public function gcv(): Response
    {
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route({
     *      "en": "/contact-us",
     *      "fr": "/contactez-nous"
     *  },
     *  name="contact_us",
     *  methods={"GET", "POST"}
     * )
     */
    public function contactUs(): Response
    {
        return $this->redirectToRoute('homepage');
    }
}
