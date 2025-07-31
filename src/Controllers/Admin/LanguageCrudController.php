<?php

namespace App\Controllers\Admin;

use App\Entities\Language;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LanguageCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Language::class;
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
