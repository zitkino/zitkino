<?php

namespace App\Models\Page;

use Dobine\Entities\DobineEntity;
use Dobine\Properties\{Dates\Dateable, Iconable, Ids\Id, Ids\Identable, Knp\Translatable as KnpTranslatable, Sortable};
use Doctrine\Common\Collections\{ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Page
 *
 * @method PageTranslation translate(?string $locale = null, bool $fallbackToDefault = true)
 */
#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: "zk_pages")]
#[ORM\HasLifecycleCallbacks]
class Page extends DobineEntity implements TranslatableInterface {
	use Id, Identable, Iconable, Sortable, Dateable, KnpTranslatable;
	
	public function __construct() {
		$this->currentLocale = "en";
		$this->created = new \DateTime();
		$this->translations = new ArrayCollection();
	}
}
