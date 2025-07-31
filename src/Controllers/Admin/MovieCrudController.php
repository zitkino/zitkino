<?php

namespace App\Controllers\Admin;

use App\Entities\Movie;
use EasyCorp\Bundle\EasyAdminBundle\Config\{Action, Actions, Crud};
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, IntegerField, TextField};

class MovieCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Movie::class;
	}
	
	public function configureFields(string $pageName): iterable {
		return [
			IdField::new("id"),
			TextField::new("name"),
			IntegerField::new("length")->setRequired(false),
			TextField::new("csfd")->setRequired(false),
			TextField::new("imdb")->setRequired(false),
		];
	}
	
	public function configureActions(Actions $actions): Actions {
		return $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
	}
	
	public function configureCrud(Crud $crud): Crud {
		return $crud->showEntityActionsInlined();
	}
}
