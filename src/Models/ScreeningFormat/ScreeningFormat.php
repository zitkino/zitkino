<?php

namespace App\Models\ScreeningFormat;

use Dobine\Properties\Ids\{Id, Identable};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScreeningFormatRepository::class)]
#[ORM\Table(name: "zk_screenings_formats")]
class ScreeningFormat {
	use Id, Identable;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
	public ?string $name = null {
		get => $this->name;
		set => $this->name = $value;
	}
	
	public function __construct(string $ident) {
		$this->setIdent($ident);
		$this->name = $ident;
	}
	
	public function __toString() {
		return $this->ident;
	}
}
