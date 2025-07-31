<?php

namespace App\Controllers\Admin;

use App\Entities\Showtime;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class ShowtimeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Showtime::class;
	}
	
	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new("id"),
			DateTimeField::new("datetime"),
			
			AssociationField::new("screening")
				->setRequired(true)
				->setHelp("Select the screening for this showtime"),
		];
	}
	
}
