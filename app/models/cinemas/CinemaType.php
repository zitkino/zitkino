<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\DobineEntity;
use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;

/**
 * CinemaType
 *
 * @ORM\Table(name="cinemas_types", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class CinemaType {
	use Identifier;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=255, nullable=false)
	 */
	protected $code;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
	
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
	}
	
	
	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 * @return CinemaType
	 */
	public function setName(string $name): CinemaType {
		$this->name = $name;
		return $this;
	}
}
