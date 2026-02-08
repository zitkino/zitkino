<?php

namespace App\Controllers\Admin;

use App\EasyAdmin\Translations\TranslationsField;
use App\Models\Entities\Language;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, SlugField, TextField};

class LanguageCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Language::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id")
			->hideWhenCreating();
		yield TextField::new("name");
		yield SlugField::new("ident")
			->setTargetFieldName("name");
		yield TextField::new("icon");
		
		yield TranslationsField::new("translations")
			->addTranslatableField(TextField::new("dubbing")
				->setRequired(true))
			->addTranslatableField(TextField::new("subtitles")
				->setRequired(true));
	}
}
