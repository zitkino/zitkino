<?php
namespace Zitkino\Screenings;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;

/**
 * ScreeningType
 *
 * @ORM\Table(name="screenings_types", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class ScreeningType {
	use Identifier;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=10, nullable=false)
	 */
	private $code;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;
	
	public function __construct(string $code) {
		$this->code = $code;
		$this->name = $code;
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}
	
	/**
	 * @param string $code
	 * @return ScreeningType
	 */
	public function setCode(string $code): ScreeningType {
		$this->code = $code;
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getName(): ?string {
		return $this->name;
	}
	
	/**
	 * @param null|string $name
	 * @return ScreeningType
	 */
	public function setName(?string $name): ScreeningType {
		$this->name = $name;
		return $this;
	}
}
