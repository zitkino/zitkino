<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\DobineEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * CinemaType
 *
 * @ORM\Table(name="cinemas_types")
 * @ORM\Entity
 */
class CinemaType extends DobineEntity {
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
	
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	
	public function __construct(string $name) {
		$this->name = $name;
	}
}
