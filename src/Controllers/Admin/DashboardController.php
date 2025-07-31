<?php

namespace App\Controllers\Admin;

use App\Entities\Cinema;
use App\Entities\CinemaType;
use App\Entities\Language;
use App\Entities\Movie;
use App\Entities\Place;
use App\Entities\Screening;
use App\Entities\ScreeningType;
use App\Entities\Showtime;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Dashboard, MenuItem};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: "/admin", routeName: "admin")]
class DashboardController extends AbstractDashboardController {
	public function index(): Response {
		return parent::index();
		
		// Option 1. You can make your dashboard redirect to some common page of your backend
		//
		// 1.1) If you have enabled the "pretty URLs" feature:
		// return $this->redirectToRoute("admin_user_index");
		//
		// 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
		// $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
		// return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());
		
		// Option 2. You can make your dashboard redirect to different pages depending on the user
		//
		// if ("jane" === $this->getUser()->getUsername()) {
		//     return $this->redirectToRoute("...");
		// }
		
		// Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
		// (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
		//
		// return $this->render("some/path/my-dashboard.html.twig");
	}
	
	public function configureDashboard(): Dashboard {
		return Dashboard::new()
			->setTitle("Žít kino - Admin");
	}
	
	public function configureMenuItems(): iterable {
		yield MenuItem::linkToDashboard("Dashboard", "fa fa-home");
		
		yield MenuItem::section("Cinemas");
		yield MenuItem::linkToCrud("Cinema", "fas fa-building", Cinema::class);
		yield MenuItem::linkToCrud("Cinema Type", "fas fa-warehouse", CinemaType::class);
		yield MenuItem::linkToCrud("Place", "fas fa-location-dot", Place::class);
		
		yield MenuItem::section("Movies");
		yield MenuItem::linkToCrud("Movie", "fa fa-film", Movie::class);
		yield MenuItem::linkToCrud("Language", "fa fa-language", Language::class);
		yield MenuItem::linkToCrud("Screening", "fa fa-ticket", Screening::class);
		yield MenuItem::linkToCrud("Screening Type", "fas fa-ticket-simple", ScreeningType::class);
		yield MenuItem::linkToCrud("Showtime", "fas fa-calendar-alt", Showtime::class);
	}
}
