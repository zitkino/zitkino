<?php

namespace App\Models\Entities;

use Dobine\Properties\{Dates\Dateable, Ids\Id, Localable, Sluggable};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_routes")]
#[ORM\HasLifecycleCallbacks]
class Route {
	use Id, Sluggable, Localable, Dateable;
	
	#[ORM\Column(name: "canonical_route", type: "string", length: 255, nullable: false)]
	public string $canonical_route {
		get => $this->canonical_route;
		set => $this->canonical_route = $value;
	}
	
	#[ORM\Column(name: "controller", type: "string", length: 255, nullable: false)]
	public string $controller {
		get => $this->controller;
		set => $this->controller = $value;
	}
	
	#[ORM\Column(name: "entity_id", type: "integer", nullable: true)]
	public ?int $entityId = null {
		get => $this->entityId;
		set => $this->entityId = $value;
	}
	
	#[ORM\Column(name: "entity_class", type: "string", length: 255, nullable: true)]
	public ?string $entityClass = null {
		get => $this->entityClass;
		set => $this->entityClass = $value;
	}
	
	public function __construct() {
		$this->created = new \DateTime();
	}
}
