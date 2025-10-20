<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: "`zk_cinemas_translations`")]
class CinemaTranslation implements TranslationInterface {
	use Id, KnpTranslation;
	
	#[ORM\Column(name: "text", type: "text", length: 0, nullable: true)]
	protected ?string $text = null;
	
	public function getText(): ?string {
		return $this->text;
	}
	
	public function setText(?string $text): CinemaTranslation {
		$this->text = $text;
		return $this;
	}
}
