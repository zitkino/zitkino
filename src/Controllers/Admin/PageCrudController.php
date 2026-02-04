<?php

namespace App\Controllers\Admin;

use App\EasyAdmin\Translations\TranslationsField;
use App\Models\Entities\Page;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{BooleanField, DateTimeField, Field, IdField, IntegerField, SlugField, TextField};
use EmilePerron\TinymceBundle\Form\Type\TinymceType;

class PageCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Page::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id")
			->hideWhenCreating();
//		yield TextField::new("name");
		yield TextField::new("ident");
		yield TextField::new("icon");
		yield IntegerField::new("order");
		yield BooleanField::new("visible")
			->setHelp("Indicates if the page is visible on the site");
		yield DateTimeField::new('created');
		yield DateTimeField::new("updated")
			->setDisabled();
		
		yield TranslationsField::new('translations')
			->addTranslatableField(TextField::new("title")
				->setRequired(true))
			->addTranslatableField(SlugField::new("slug")
				->setRequired(true)
				->setTargetFieldName("title"))
			->addTranslatableField(Field::new('perex')
				->setRequired(false)
				->setFormType(TinymceType::class))
			->addTranslatableField(Field::new('text')
				->setRequired(false)
				->setFormType(TinymceType::class));
	}
}
