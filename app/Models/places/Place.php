<?php
namespace Zitkino;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;
use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\Screenings;

/**
 * Place
 *
 * @ORM\Table(name="places")
 * @ORM\Entity
 */
class Place {
	use Id;
	
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
	 * @ORM\ManyToOne(targetEntity="\Zitkino\Cinemas\Cinema", inversedBy="places")
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
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): Place {
		$this->name = $name;
		return $this;
	}
	
	public function getLink(): ?string {
		return $this->link;
	}
	
	public function setLink(?string $link): Place {
		$this->link = $link;
		return $this;
	}
	
	public function getCinema(): Cinema {
		return $this->cinema;
	}
	
	public function setCinema(Cinema $cinema): Place {
		$this->cinema = $cinema;
		return $this;
	}
	
	public function getScreenings(): Screenings {
		return $this->screenings;
	}
	
	public function setScreenings(Screenings $screenings): Place {
		$this->screenings = $screenings;
		return $this;
	}
}
