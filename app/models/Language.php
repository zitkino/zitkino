<?php
namespace Zitkino;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * Language
 *
 * @ORM\Table(name="languages")
 * @ORM\Entity
 */
class Language {
	use MagicAccessors;
	
	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
	/**
	 * @var string|null
	 * @ORM\Column(name="code", type="string", length=10, nullable=true)
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
}
