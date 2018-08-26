<?php
namespace Zitkino\Screenings;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * ScreeningType
 *
 * @ORM\Table(name="screenings_types")
 * @ORM\Entity
 */
class ScreeningType {
	use Identifier;
	
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
	
	/**
	 * @param string $name
	 * @return ScreeningType
	 */
	public function setName(string $name): ScreeningType {
		$this->name = $name;
		return $this;
	}
}
