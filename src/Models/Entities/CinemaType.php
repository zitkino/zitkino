<?php

namespace App\Models\Entities;

use Dobine\Properties\{Iconable, Ids\Id, Ids\Identable, Knp\Translatable as KnpTranslatable, Sortable};
use Doctrine\Common\Collections\{ArrayCollection, Collection};
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

#[ORM\Entity]
#[ORM\Table(name: "zk_cinemas_types")]
class CinemaType implements TranslatableInterface {
	use Id, Identable, Iconable, Sortable, KnpTranslatable;
	
	#[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
	private string $name;
	
	#[ORM\OneToMany(mappedBy: "type", targetEntity: "Cinema")]
	private Collection $cinemas;
	
	public function __construct(string $ident) {
		$this->ident = $ident;
		$this->name = $ident;
		$this->cinemas = new ArrayCollection();
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): self {
		$this->name = $name;
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
