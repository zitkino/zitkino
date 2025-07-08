<?php

namespace App\Controller;

use App\Service\CinemaFacade;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Homepage controller.
 */
class HomeController extends AbstractController
{
    /** @var CinemaFacade */
    private $cinemaFacade;

    /**
     * @param CinemaFacade $cinemaFacade
     */
    public function __construct(
        CinemaFacade $cinemaFacade,
        \Symfony\Contracts\Translation\TranslatorInterface $translator,
        \Symfony\Component\HttpFoundation\RequestStack $requestStack,
        \App\Service\MetaService $metaService,
        \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
    ) {
        parent::__construct($translator, $requestStack, $metaService, $parameterBag);
        $this->cinemaFacade = $cinemaFacade;
    }

    /**
     * @Route("/", name="home_default")
     */
    public function index(): Response
    {
        $cinemas = $this->cinemaFacade->getWithMovies("current");

        return $this->render('home/default.html.twig', [
            'cinemas' => $cinemas
        ]);
    }

    /**
     * @Route("/mapa", name="home_map")
     */
    public function map(): Response
    {
        return $this->render('home/map.html.twig');
    }

    /**
     * @Route("/kontakt", name="home_contact")
     */
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    /**
     * @Route("/info", name="home_about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig');
    }
}
