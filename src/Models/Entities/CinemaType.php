<?php

namespace App\Models\Entities;

use Dobine\Properties\{Ids\Id, Knp\Translatable as KnpTranslatable, Sortable};
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

#[ORM\Entity]
#[ORM\Table(name: "zk_cinemas_types", uniqueConstraints: [new ORM\UniqueConstraint(name: "code", columns: ["code"])])]
class CinemaType implements TranslatableInterface {
	use Id, Sortable, KnpTranslatable;
	
	#[ORM\Column(name: "code", type: "string", length: 191, nullable: false)]
	private string $code;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	private string $name;
	
	#[ORM\Column(name: "icon", type: "string", length: 255, nullable: true)]
	private string $icon;
	
	#[ORM\OneToMany(mappedBy: "type", targetEntity: "Cinema")]
	private Collection $cinemas;
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
		$this->cinemas = new ArrayCollection();
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
	
	public function getIcon(): ?string {
		return $this->icon;
	}
	
	public function setIcon(?string $icon): self {
		$this->icon = $icon;
		return $this;
	}
	
	public function getCinemas(): Collection {
		return $this->cinemas;
	}
	
	public function setCinemas(Collection $cinemas): self {
		$this->cinemas = $cinemas;
		return $this;
	}
	
	public function __toString(): string {
		return $this->getName();
	}
}
