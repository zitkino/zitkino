<?php

namespace App\Models\Language;

use Dobine\Properties\{Iconable, Ids\Id, Ids\Identable, Knp\Translatable as KnpTranslatable};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

/**
 * Language
 * @method LanguageTranslation translate(?string $locale = null, bool $fallbackToDefault = true)
 */
#[ORM\Entity(repositoryClass: LanguageRepository::class)]
#[ORM\Table(name: "zk_languages")]
class Language implements TranslatableInterface {
	use Id, Identable, Iconable, KnpTranslatable;
	
	#[ORM\Column(name: "name", type: "string", length: 191, nullable: false)]
	public string $name {
		get => $this->name;
		set => $this->name = $value;
	}
	
	public function __construct(string $ident) {
		$this->ident = $ident;
		$this->translations = new ArrayCollection();
	}
}
