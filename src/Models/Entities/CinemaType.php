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
	public string $name {
		get => $this->name;
		set => $this->name = $value;
	}
	
	#[ORM\OneToMany(mappedBy: "type", targetEntity: "Cinema")]
	public Collection $cinemas {
		get => $this->cinemas;
		set => $this->cinemas = $value;
	}
	
	public function __construct(string $ident) {
		$this->ident = $ident;
		$this->name = $ident;
		$this->cinemas = new ArrayCollection();
	}
	
	public function __toString(): string {
		return $this->name;
	}
}
