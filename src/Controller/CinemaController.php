<?php

namespace App\Controller;

use App\Service\CinemaFacade;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Cinema controller.
 */
class CinemaController extends AbstractController
{
    /** @var CinemaFacade */
    private $cinemaFacade;

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
     * @Route("/kina", name="cinema_default")
     * @Route("/kino", name="cinema_default_alt")
     */
    public function index(): Response
    {
        return $this->render('cinema/default.html.twig', [
            'classicCinemas' => $this->cinemaFacade->getByType("classic"),
            'multiplexCinemas' => $this->cinemaFacade->getByType("multiplex"),
            'summerCinemas' => $this->cinemaFacade->getByType("summer")
        ]);
    }

    /**
     * @Route("/kino/{id}", name="cinema_profile")
     * 
     * @param string|int $id
     */
    public function profile($id): Response
    {
        $cinema = $this->cinemaFacade->getById($id);
        $screenings = $cinema->getNewScreenings();

        $gmaps = $cinema->getGmaps();
        if ($gmaps === null) {
            $address = $cinema->getAddress() . ", " . $cinema->getCity();
            $param = urlencode($address);
        } else {
            $param = "place_id:" . $gmaps;
        }

        return $this->render('cinema/profile.html.twig', [
            'cinema' => $cinema,
            'screenings' => $screenings,
            'gmap' => $param,
            'gmapKey' => $this->parameterBag->get('google-maps-key')
        ]);
    }

    /**
     * @Route("/klasicka", name="cinema_type_classic")
     * @Route("/klasicky", name="cinema_type_classic_alt")
     * @Route("/multiplexy", name="cinema_type_multiplex")
     * @Route("/multiplex", name="cinema_type_multiplex_alt")
     * @Route("/letni", name="cinema_type_summer")
     * 
     * @param string $type
     */
    public function type(string $type = null): Response
    {
        // Determine type from route
        if ($type === null) {
            $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
            if (strpos($route, 'classic') !== false) {
                $type = 'classic';
            } elseif (strpos($route, 'multiplex') !== false) {
                $type = 'multiplex';
            } elseif (strpos($route, 'summer') !== false) {
                $type = 'summer';
            }
        }

        return $this->render('cinema/type.html.twig', [
            'cinemas' => $this->cinemaFacade->getByType($type),
            'type' => $type
        ]);
    }

    /**
     * @Route("/klasicky/program", name="cinema_programme_classic")
     * @Route("/klasicka/program", name="cinema_programme_classic_alt")
     * @Route("/multiplexy/program", name="cinema_programme_multiplex")
     * @Route("/multiplex/program", name="cinema_programme_multiplex_alt")
     * @Route("/letni/program", name="cinema_programme_summer")
     * 
     * @param string $type
     */
    public function programme(string $type = null): Response
    {
        // Determine type from route
        if ($type === null) {
            $route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
            if (strpos($route, 'classic') !== false) {
                $type = 'classic';
            } elseif (strpos($route, 'multiplex') !== false) {
                $type = 'multiplex';
            } elseif (strpos($route, 'summer') !== false) {
                $type = 'summer';
            }
        }

        return $this->render('cinema/programme.html.twig', [
            'cinemas' => $this->cinemaFacade->getWithMovies($type),
            'type' => $type
        ]);
    }
}
