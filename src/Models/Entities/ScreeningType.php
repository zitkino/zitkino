<?php

namespace App\Models\Entities;

use App\Models\Repositories\ScreeningTypeRepository;
use Dobine\Properties\Ids\Id;
use Dobine\Properties\Ids\Identable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScreeningTypeRepository::class)]
#[ORM\Table(name: "zk_screenings_types")]
class ScreeningType {
	use Id, Identable;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
	private ?string $name = null;
	
	public function __construct(string $code) {
		$this->setIdent($code);
		$this->name = $code;
	}
	
	public function __toString() {
		return $this->getIdent();
	}
	
	public function getName(): ?string {
		return $this->name;
	}
	
	public function setName(?string $name): self {
		$this->name = $name;
		return $this;
	}
}
