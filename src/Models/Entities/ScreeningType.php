<?php

namespace App\Models\Entities;

use App\Models\Repositories\ScreeningTypeRepository;
use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ScreeningTypeRepository::class)]
#[ORM\Table(name: "zk_screenings_types", uniqueConstraints: [new ORM\UniqueConstraint(name: "code", columns: ["code"])])]
class ScreeningType {
	use Id;
	
	#[ORM\Column(name: "code", type: "string", length: 191, nullable: false)]
	private string $code;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
	private ?string $name = null;
	
	public function __construct(string $code) {
		$this->setCode($code);
		$this->name = $code;
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function setCode(string $code): self {
		$slugger = new AsciiSlugger();
		$this->code = $slugger->slug($code)
			->lower()
			->toString();
		return $this;
	}
	
	public function getName(): ?string {
		return $this->name;
	}
	
	public function setName(?string $name): self {
		$this->name = $name;
		return $this;
	}
}
