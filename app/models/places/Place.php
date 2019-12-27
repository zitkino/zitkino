<?php
namespace Zitkino;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;
use Nette\Utils\Strings;
use Zitkino\Screenings\Screenings;

/**
 * Place
 *
 * @ORM\Table(name="place", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Place {
	use Identifier, MagicAccessors;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=10, nullable=false)
	 */
	protected $code;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="link", type="string", length=255, nullable=true)
	 */
	protected $link;
	
	/**
	 * @var Screenings
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Screening", mappedBy="place", cascade={"persist", "remove"})
	 */
	private $screenings;
	
	public function __construct(string $name) {
		$this->name = $name;
		$this->code = Strings::webalize($name);
	}
	
	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}
	
	/**
	 * @param string $code
	 * @return Place
	 */
	public function setCode(string $code): Place {
		$this->code = $code;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 * @return Place
	 */
	public function setName(string $name): Place {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getLink(): ?string {
		return $this->link;
	}
	
	/**
	 * @param string|null $link
	 * @return Place
	 */
	public function setLink(?string $link): Place {
		$this->link = $link;
		return $this;
	}
}
