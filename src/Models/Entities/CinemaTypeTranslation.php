<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: "`zk_cinemas_types_translations`")]
class CinemaTypeTranslation implements TranslationInterface {
	use Id, KnpTranslation;
	
	#[ORM\Column(name: "title", type: "string", length: 191, nullable: false)]
	protected string $title;
	
	#[ORM\Column(name: "text", type: "text", length: 0, nullable: true)]
	protected ?string $text = null;
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function setTitle(?string $title): CinemaTypeTranslation {
		$this->title = $title;
		return $this;
	}
	
	public function getText(): ?string {
		return $this->text;
	}
	
	public function setText(?string $text): CinemaTypeTranslation {
		$this->text = $text;
		return $this;
	}
}
