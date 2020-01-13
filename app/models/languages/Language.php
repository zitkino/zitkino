<?php
namespace Zitkino;

use Dobine\Entities\Identifier;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * Language
 *
 * @ORM\Table(name="languages", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Language {
	use Identifier;
	
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
	
	
	/**
	 * @return string
	 */
	public function getCode(): string {
		return $this->code;
	}
	
	/**
	 * @return null|string
	 */
	public function getCzech(): ?string {
		return $this->czech;
	}
	
	/**
	 * @param null|string $czech
	 * @return Language
	 */
	public function setCzech(?string $czech): Language {
		$this->czech = $czech;
		return $this;
	}
	
	/**
	 * @return null|string
	 */
	public function getEnglish(): ?string {
		return $this->english;
	}
	
	/**
	 * @param null|string $english
	 * @return Language
	 */
	public function setEnglish(?string $english): Language {
		$this->english = $english;
		return $this;
	}
}
