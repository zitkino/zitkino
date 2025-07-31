<?php

namespace App\Controllers\Admin;

use App\Entities\CinemaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CinemaTypeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return CinemaType::class;
	}
	/*
	public function configureFields(string $pageName): iterable
	{
		return [
			IdField::new("id"),
			TextField::new("title"),
			TextEditorField::new("description"),
		];
	}
	*/
}
