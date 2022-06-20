<?php
namespace Zitkino\Screenings;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;

/**
 * ScreeningType
 *
 * @ORM\Table(name="screenings_types", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class ScreeningType {
	use Id;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=191, nullable=false)
	 */
	private $code;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="name", type="string", length=255, nullable=true)
	 */
	private $name;
	
	public function __construct(string $code) {
		$this->setCode($code);
		$this->name = $code;
	}
	
	public function __toString() {
		return $this->getCode();
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function setCode(string $code): ScreeningType {
		$this->code = Strings::webalize($code);
		return $this;
	}
	
	public function getName(): ?string {
		return $this->name;
	}
	
	public function setName(?string $name): ScreeningType {
		$this->name = $name;
		return $this;
	}
}
