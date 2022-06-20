<?php
namespace Zitkino\Cinemas;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * CinemaType
 *
 * @ORM\Table(name="cinemas_types", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class CinemaType {
	use Id;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=191, nullable=false)
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
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): CinemaType {
		$this->name = $name;
		return $this;
	}
}
