<?php

namespace App\Controllers\Admin;

use App\Models\ScreeningType\ScreeningType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ScreeningTypeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return ScreeningType::class;
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
