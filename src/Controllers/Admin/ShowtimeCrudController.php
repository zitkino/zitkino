<?php

namespace App\Controllers\Admin;

use App\Models\Entities\Showtime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, DateTimeField, IdField};

class ShowtimeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Showtime::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id");
		yield DateTimeField::new("datetime");
		
		yield AssociationField::new("screening")
			->setRequired(true)
			->setHelp("Select the screening for this showtime");
	}
}
