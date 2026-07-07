<?php

namespace App\Controllers\Admin;

use App\Models\Movie\Movie;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, IntegerField, TextField};

class MovieCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Movie::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id");
		yield TextField::new("name");
		yield IntegerField::new("length")
			->setRequired(false);
		yield TextField::new("csfd")
			->setRequired(false);
		yield TextField::new("imdb")
			->setRequired(false);
	}
	
	public function configureActions(Actions $actions): Actions {
		return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
	}
}
