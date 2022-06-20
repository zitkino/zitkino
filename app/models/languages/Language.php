<?php
namespace Zitkino;

use Dobine\Attributes\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table(name="languages", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Language {
	use Id;
	
	/**
	 * @var string
	 * @ORM\Column(name="code", type="string", length=10, nullable=false)
	 */
	protected $code;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="czech", type="string", length=10, nullable=true)
	 */
	protected $czech;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="english", type="string", length=10, nullable=true)
	 */
	protected $english;
	
	public function __construct(string $code) {
		$this->code = $code;
	}
	
	public function getCode(): string {
		return $this->code;
	}
	
	public function getCzech(): ?string {
		return $this->czech;
	}
	
	public function setCzech(?string $czech): Language {
		$this->czech = $czech;
		return $this;
	}
	
	public function getEnglish(): ?string {
		return $this->english;
	}
	
	public function setEnglish(?string $english): Language {
		$this->english = $english;
		return $this;
	}
}
