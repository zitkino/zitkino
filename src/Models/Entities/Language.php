<?php

namespace App\Models\Entities;

use App\Models\Repositories\LanguageRepository;
use Dobine\Properties\Ids\Id;
use Dobine\Properties\Ids\Identable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
#[ORM\Table(name: "zk_languages")]
class Language {
	use Id, Identable;
	
	#[ORM\Column(name: "czech", type: "string", length: 10, nullable: true)]
	private ?string $czech = null;
	
	#[ORM\Column(name: "english", type: "string", length: 10, nullable: true)]
	private ?string $english = null;
	
	public function __construct(string $ident) {
		$this->ident = $ident;
	}
	
	public function getCzech(): ?string {
		return $this->czech;
	}
	
	public function setCzech(?string $czech): self {
		$this->czech = $czech;
		return $this;
	}
	
	public function getEnglish(): ?string {
		return $this->english;
	}
	
	public function setEnglish(?string $english): self {
		$this->english = $english;
		return $this;
	}
}
