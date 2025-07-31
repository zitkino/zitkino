<?php

namespace App\Controllers\Admin;

use App\Entities\Place;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, TextField, UrlField};

class PlaceCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Place::class;
	}
	
	public function configureFields(string $pageName): iterable {
		return [
			IdField::new("id"),
			TextField::new("name"),
			AssociationField::new("cinema"),
			UrlField::new("link")->setRequired(false),
		];
	}
}
