<?php

namespace App\Controllers\Admin;

use App\EasyAdmin\Translations\TranslationsField;
use App\Models\CinemaType\CinemaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EmilePerron\TinymceBundle\Form\Type\TinymceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\{BooleanField, IdField, IntegerField, SlugField, TextEditorField, TextField};

class CinemaTypeCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return CinemaType::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id")->hideWhenCreating();
		yield TextField::new("name");
		yield SlugField::new("ident")
			->setTargetFieldName("name");
		yield TextField::new("icon");
		
		yield IntegerField::new("order");
		yield BooleanField::new("visible")
			->setHelp("Indicates if it is shown on the site");
		
		yield TranslationsField::new("translations")
			->addTranslatableField(TextField::new("title")
				->setRequired(true))
			->addTranslatableField(SlugField::new("slug")->setTargetFieldName("title"))
				->setRequired(true)
			->addTranslatableField(TextEditorField::new("text")
				->setFormType(TinymceType::class)
				->setRequired(false));
	}
}
