<?php

namespace App\Controllers\Admin;

use App\Models\Screening\Screening;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\{AssociationField, IdField, MoneyField, TextField, UrlField};

class ScreeningCrudController extends AbstractCrudController {
	public static function getEntityFqcn(): string {
		return Screening::class;
	}
	
	public function configureFields(string $pageName): iterable {
		yield IdField::new("id");
		yield AssociationField::new("movie");
		yield AssociationField::new("cinema");
		yield AssociationField::new("type");
		yield AssociationField::new("place");
		yield TextField::new("dubbing");
		yield TextField::new("subtitles");
		yield MoneyField::new("price")
			->setCurrency("CZK")
			->setStoredAsCents(false)
			->setNumDecimals(0);
		yield UrlField::new("link");
	}
}
