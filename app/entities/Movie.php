<?php
namespace Zitkino\Entities;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Movie
 *
 * @ORM\Table(name="movies")
 * @ORM\Entity
 */
class Movie extends BaseEntity {
    use Identifier;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var int|null
     *
     * @ORM\Column(name="length", type="integer", nullable=true)
     */
    private $length;

    /**
     * @var string|null
     *
     * @ORM\Column(name="csfd", type="string", length=255, nullable=true)
     */
    private $csfd;

    /**
     * @var string|null
     *
     * @ORM\Column(name="imdb", type="string", length=255, nullable=true)
     */
    private $imdb;
}
