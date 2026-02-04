<?php

namespace App\Models\Entities;

use App\Models\Repositories\RouteRepository;
use Dobine\Properties\{Dates\Dateable, Ids\Id, Localable, Sluggable};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
#[ORM\Table(name: "zk_routes")]
#[ORM\HasLifecycleCallbacks]
class Route {
	use Id, Sluggable, Localable, Dateable;
	
	#[ORM\Column(name: "canonical_route", type: "string", length: 255, nullable: false)]
	private string $canonical_route;
	
	#[ORM\Column(name: "controller", type: "string", length: 255, nullable: false)]
	private string $controller;
	
	#[ORM\Column(name: "entity_id", type: "integer", nullable: true)]
	private ?int $entityId = null;
	
	#[ORM\Column(name: "entity_class", type: "string", length: 255, nullable: true)]
	private ?string $entityClass = null;
	
	public function __construct() {
		$this->created = new \DateTime();
	}
	
	public function getCanonicalRoute(): string {
		return $this->canonical_route;
	}
	
	public function setCanonicalRoute(string $canonical_route): self {
		$this->canonical_route = $canonical_route;
		return $this;
	}
	
	public function getController(): string {
		return $this->controller;
	}
	
	public function setController(string $controller): self {
		$this->controller = $controller;
		return $this;
	}
	
	public function getEntityId(): ?int {
		return $this->entityId;
	}
	
	public function setEntityId(?int $entityId): self {
		$this->entityId = $entityId;
		return $this;
	}
	
	public function getEntityClass(): ?string {
		return $this->entityClass;
	}
	
	public function setEntityClass(?string $entityClass): self {
		$this->entityClass = $entityClass;
		return $this;
	}
}
