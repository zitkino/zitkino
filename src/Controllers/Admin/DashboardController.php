<?php

namespace App\Controllers\Admin;

use App\Models\Entities\{Cinema, CinemaType, Language, Movie, Place, Screening, ScreeningType, Showtime};
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Asset, Assets, Crud, Dashboard, MenuItem};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: "/admin", routeName: "admin")]
#[IsGranted("ROLE_ADMIN")]
class DashboardController extends AbstractDashboardController {
	public function index(): Response {
		return $this->render("admin/dashboard.html.twig");
	}
	
	public function configureAssets(): Assets {
		return Assets::new()
			->useCustomIconSet("bi")
			->addJsFile("js/tiny-mce.js");
	}
	
	public function configureCrud(): Crud {
		return Crud::new()
			->showEntityActionsInlined()
			->setFormThemes(["@Tinymce/form/tinymce_type.html.twig", "@EasyAdmin/crud/form_theme.html.twig"]);
	}
	
	public function configureDashboard(): Dashboard {
		return Dashboard::new()
			->setTitle("Žít kino - Admin");
	}
	
	public function configureMenuItems(): iterable {
		yield MenuItem::linkToDashboard("Dashboard", "house-door-fill");
		
		yield MenuItem::section("Cinemas");
		yield MenuItem::linkToCrud("Cinema", "camera-reels", Cinema::class);
		yield MenuItem::linkToCrud("Cinema Type", "building-gear", CinemaType::class);
		yield MenuItem::linkToCrud("Place", "door-open", Place::class);
		
		yield MenuItem::section("Movies");
		yield MenuItem::linkToCrud("Movie", "film", Movie::class);
		yield MenuItem::linkToCrud("Language", "translate", Language::class);
		yield MenuItem::linkToCrud("Screening", "ticket", Screening::class);
		yield MenuItem::linkToCrud("Screening Type", "ticket-detailed", ScreeningType::class);
		yield MenuItem::linkToCrud("Showtime", "calendar2-week", Showtime::class);
	}
}
