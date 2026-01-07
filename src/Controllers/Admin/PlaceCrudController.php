<?php

namespace App\Controllers\Admin;

use App\Models\Entities\Place;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, TextField, UrlField};

class PlaceCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Place::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id");
		yield TextField::new("name");
		yield AssociationField::new("cinema");
		yield UrlField::new("link")
			->setRequired(false);
	}
}
