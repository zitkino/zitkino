<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translation as KnpTranslation};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: "`zk_languages_translations`")]
class LanguageTranslation implements TranslationInterface {
	use Id, KnpTranslation;
	
	#[ORM\Column(name: "dubbing", type: "string", length: 191, nullable: false)]
	protected string $dubbing;
	
	#[ORM\Column(name: "subtitles", type: "string", length: 191, nullable: false)]
	protected string $subtitles;
	
	public function getDubbing(): string {
		return $this->dubbing;
	}
	
	public function setDubbing(?string $dubbing): LanguageTranslation {
		$this->dubbing = $dubbing;
		return $this;
	}
	
	public function getSubtitles(): string {
		return $this->subtitles;
	}
	
	public function setSubtitles(?string $subtitles): LanguageTranslation {
		$this->subtitles = $subtitles;
		return $this;
	}
}
