<?php

namespace App\Controllers\Admin;

use App\Models\Entities\Cinema;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, BooleanField, DateTimeField, FormField, IdField, IntegerField, SlugField, TelephoneField, TextField, UrlField};

class CinemaCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Cinema::class;
	}
	
	public function configureFields(string $pageName): iterable {
		return [
			FormField::addTab('Basic Information'),
			FormField::addColumn(8),
			IdField::new("id"),
			TextField::new("name"),
			SlugField::new("code")->setTargetFieldName("name"),
			AssociationField::new("type")
				->setCrudController(CinemaTypeCrudController::class)
				->setRequired(true)
				->setHelp("Select the type of cinema"),
			
			FormField::addColumn(4),
			DateTimeField::new("activeSince")
				->setRequired(false)
				->setHelp("Date when the cinema became active"),
			DateTimeField::new("activeUntil")
				->setRequired(false)
				->setHelp("Date when the cinema stopped being active"),
			IntegerField::new("order"),
			BooleanField::new("visible")
				->setHelp("Indicates if the cinema is visible on the site"),
			
			
			FormField::addTab('Location Information'),
			FormField::addColumn(6),
			TextField::new("address"),
			TextField::new("city"),//->setDefault("Brno"),
			TelephoneField::new("phone")->hideOnIndex()
				->setRequired(false),
			TextField::new("email")->hideOnIndex()
				->setRequired(false),
			
			FormField::addColumn(6),
			UrlField::new("url")->hideOnIndex()
				->setRequired(false),
			UrlField::new("programme")->hideOnIndex()
				->setRequired(false),
			TextField::new("gmaps")->hideOnIndex()
				->setRequired(false),
			
			
			FormField::addTab('Social Media'),
			TextField::new("facebook")->hideOnIndex()
				->setRequired(false),
			TextField::new("googlePlus")->hideOnIndex()
				->setRequired(false),
			TextField::new("instagram")->hideOnIndex()
				->setRequired(false),
			TextField::new("twitter")->hideOnIndex()
				->setRequired(false),
			
			FormField::addTab('Parsing Information'),
			BooleanField::new("parsable")
				->setRequired(false)
				->setHelp("Indicates if the cinema is parsable"),
			UrlField::new("parsing")
				->setRequired(false)
				->setHelp("URL for parsing the cinema data"),
			DateTimeField::new("parsed")
				->setRequired(false)
		];
	}
}
