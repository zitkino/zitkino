<?php

namespace App\Controllers\Admin;

use App\EasyAdmin\Translations\TranslationsField;
use App\Models\Entities\CinemaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\{IdField, SlugField, TextEditorField, TextField};

class CinemaTypeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return CinemaType::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id");
		yield TextField::new("name");
		yield SlugField::new("code")
			->setTargetFieldName("name");
		yield TranslationsField::new("translations")
			->addTranslatableField(TextField::new("title")
				->setRequired(true))
			->addTranslatableField(TextEditorField::new("text")
				->setFormType(TinymceType::class)
				->setRequired(false));
	}
}
