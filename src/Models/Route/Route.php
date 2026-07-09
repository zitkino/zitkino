<?php

namespace App\Models\Route;

use Dobine\Properties\{Dates\Dateable, Ids\Id, Localable, Sluggable};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "zk_routes")]
#[ORM\HasLifecycleCallbacks]
class Route {
	use Id, Sluggable, Localable, Dateable;
	
	#[ORM\Column(name: "canonical_route", type: "string", length: 255, nullable: false)]
	public string $canonicalRoute {
		get => $this->canonicalRoute;
		set => $this->canonicalRoute = $value;
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
	
	public function getCanonicalRoute(): string {
		return $this->canonicalRoute;
	}
	
	public function setCanonicalRoute(string $canonicalRoute): Route {
		$this->canonicalRoute = $canonicalRoute;
		return $this;
	}
	
	public function getController(): string {
		return $this->controller;
	}
	
	public function setController(string $controller): Route {
		$this->controller = $controller;
		return $this;
	}
	
	public function getEntityId(): ?int {
		return $this->entityId;
	}
	
	public function setEntityId(?int $entityId): Route {
		$this->entityId = $entityId;
		return $this;
	}
	
	public function getEntityClass(): ?string {
		return $this->entityClass;
	}
	
	public function setEntityClass(?string $entityClass): Route {
		$this->entityClass = $entityClass;
		return $this;
	}
	
	
}
