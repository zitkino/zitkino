<?php

namespace App\Models\Entities;

use App\Models\Repositories\LanguageRepository;
use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
#[ORM\Table(name: "zk_languages", uniqueConstraints: [new ORM\UniqueConstraint(name: "code", columns: ["code"])])]
class Language {
	use Id;
	
	#[ORM\Column(name: "code", type: "string", length: 10, nullable: false)]
	private string $code;
	
	#[ORM\Column(name: "czech", type: "string", length: 10, nullable: true)]
	private ?string $czech= null;
	
	#[ORM\Column(name: "english", type: "string", length: 10, nullable: true)]
	private ?string $english= null;
	
	public function __construct(string $code) {
		$this->code = $code;
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function setCode(string $code): self {
		$this->code = $code;
		return $this;
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
