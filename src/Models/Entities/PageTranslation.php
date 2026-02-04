<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation, Sluggable, Textable};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

/**
 * PageTranslation
 */
#[ORM\Entity]
#[ORM\Table(name: "zk_pages_translations")]
class PageTranslation implements TranslationInterface {
	use Id, Sluggable, Textable, KnpTranslation;
	
	#[ORM\Column(name: "title", type: "string", length: 255, nullable: false)]
	private string $title;
	
	public function getTitle(): string {
		return $this->title;
	}
	
	public function setTitle(string $title): PageTranslation {
		$this->title = $title;
		return $this;
	}
}
