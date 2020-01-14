<?php
namespace Zitkino;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;
use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\Screenings;

/**
 * Place
 *
 * @ORM\Table(name="place", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Place {
	use Identifier;
	
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
	 * @var Cinema
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Cinemas\Cinema")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="cinema", referencedColumnName="id", nullable=false)
	 * })
	 */
	protected $cinema;
	
	/**
	 * @var Screenings
	 * @ORM\OneToMany(targetEntity="\Zitkino\Screenings\Screening", mappedBy="place")
	 */
	private $screenings;
	
	public function __construct(string $name) {
		$this->name = $name;
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
	
	/**
	 * @return Cinema
	 */
	public function getCinema(): Cinema {
		return $this->cinema;
	}
	
	/**
	 * @param Cinema $cinema
	 * @return Place
	 */
	public function setCinema(Cinema $cinema): Place {
		$this->cinema = $cinema;
		return $this;
	}
}
