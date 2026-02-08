<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation, Sluggable, Textable};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: "`zk_cinemas_types_translations`")]
class CinemaTypeTranslation implements TranslationInterface {
	use Id, Sluggable, Textable, KnpTranslation;
	
	#[ORM\Column(name: "title", type: "string", length: 191, nullable: false)]
	protected string $title;
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function setTitle(?string $title): CinemaTypeTranslation {
		$this->title = $title;
		return $this;
	}
}
