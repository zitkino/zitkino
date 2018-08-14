<?php
namespace Zitkino\Cinemas;

use Dobine\Entities\DobineEntity;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\MagicAccessors;

/**
 * CinemaType
 *
 * @ORM\Table(name="cinemas_types")
 * @ORM\Entity
 */
class CinemaType extends DobineEntity {
	use MagicAccessors;
	
	/**
	 * @var int
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;
	
	/**
	 * @var string
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	protected $name;
}
