<?php

namespace App\Entities;

use Dobine\Properties\Ids\Id;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_cinemas_types", uniqueConstraints: [new ORM\UniqueConstraint(name: "code", columns: ["code"])])]
class CinemaType {
	use Id;
	
	#[ORM\Column(name: "code", type: "string", length: 191, nullable: false)]
	private string $code;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	private string $name;
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function setCode(string $code): self {
		$this->code = $code;
		return $this;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}
}
