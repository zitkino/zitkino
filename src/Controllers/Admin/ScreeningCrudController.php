<?php

namespace App\Controllers\Admin;

use App\Entities\Screening;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, MoneyField, TextField, UrlField};

class ScreeningCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Screening::class;
	}
	
	public function configureFields(string $pageName): iterable {
		return [
			IdField::new("id"),
			AssociationField::new("movie"),
			AssociationField::new("cinema"),
			AssociationField::new("type"),
			AssociationField::new("place"),
			TextField::new("dubbing"),
			TextField::new("subtitles"),
			MoneyField::new("price")->setCurrency("CZK")->setStoredAsCents(false)->setNumDecimals(0),
			UrlField::new("link"),
		];
	}
}
