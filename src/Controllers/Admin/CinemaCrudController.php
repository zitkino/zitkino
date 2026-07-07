<?php

namespace App\Controllers\Admin;

use App\EasyAdmin\Translations\TranslationsField;
use App\Models\Cinema\Cinema;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, BooleanField, DateTimeField, Field, FormField, IdField, IntegerField, SlugField, TelephoneField, TextField, UrlField};
use EmilePerron\TinymceBundle\Form\Type\TinymceType;

class CinemaCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Cinema::class;
	}
	
	public function configureFields(string $pageName): iterable {
		/**
		 * Basic tab
		 */
		yield FormField::addTab("Basic Information");
		yield FormField::addColumn(8);
		yield IdField::new("id");
		yield TextField::new("name");
		yield SlugField::new("ident")
			->setTargetFieldName("name");
		yield SlugField::new("slug")
			->setTargetFieldName("name");
		yield AssociationField::new("type")
			->setCrudController(CinemaTypeCrudController::class)
			->setRequired(true)
			->setHelp("Select the type of cinema");
		
		yield FormField::addColumn(4);
		yield DateTimeField::new("activeSince")
			->setRequired(false)
			->setHelp("Date when the cinema became active");
		yield DateTimeField::new("activeUntil")
			->setRequired(false)
			->setHelp("Date when the cinema stopped being active");
		yield IntegerField::new("order");
		yield BooleanField::new("visible")
			->setHelp("Indicates if the cinema is visible on the site");
		
		yield FormField::addColumn(12);
		yield TranslationsField::new('translations')
			->addTranslatableField(Field::new('text')
				->setRequired(false)
				->setFormType(TinymceType::class));
		
		/**
		 * Location tab
		 */
		yield FormField::addTab("Location Information");
		yield FormField::addColumn(6);
		yield TextField::new("address");
		yield TextField::new("city");//->setDefault("Brno"),
		yield TelephoneField::new("phone")
			->hideOnIndex()
			->setRequired(false);
		yield TextField::new("email")
			->hideOnIndex()
			->setRequired(false);
		
		yield FormField::addColumn(6);
		yield UrlField::new("url")
			->hideOnIndex()
			->setRequired(false);
		yield UrlField::new("programme")
			->hideOnIndex()
			->setRequired(false);
		yield TextField::new("gmaps")
			->hideOnIndex()
			->setRequired(false);
		
		/**
		 * Social Media tab
		 */
		yield FormField::addTab("Social Media");
		yield TextField::new("facebook")
			->hideOnIndex()
			->setRequired(false);
		yield TextField::new("googlePlus")
			->hideOnIndex()
			->setRequired(false);
		yield TextField::new("instagram")
			->hideOnIndex()
			->setRequired(false);
		yield TextField::new("twitter")
			->hideOnIndex()
			->setRequired(false);
		
		/**
		 * Parsing tab
		 */
		yield FormField::addTab("Parsing Information");
		yield BooleanField::new("parsable")
			->setRequired(false)
			->setHelp("Indicates if the cinema is parsable");
		yield UrlField::new("parsing")
			->setRequired(false)
			->setHelp("URL for parsing the cinema data");
		yield DateTimeField::new("parsed")
			->setRequired(false);
	}
}
