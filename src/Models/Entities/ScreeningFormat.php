<?php

namespace App\Models\Entities;

use Dobine\Properties\Ids\{Id, Identable};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_screenings_formats")]
class ScreeningFormat {
	use Id, Identable;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
	private ?string $name = null;
	
	public function __construct(string $ident) {
		$this->setIdent($ident);
		$this->name = $ident;
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
