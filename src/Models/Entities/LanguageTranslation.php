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
	public string $dubbing {
		get => $this->dubbing;
		set => $this->dubbing = $value;
	}
	
	#[ORM\Column(name: "subtitles", type: "string", length: 191, nullable: false)]
	public string $subtitles {
		get => $this->subtitles;
		set => $this->subtitles = $value;
	}
}
