<?php

namespace Zitkino\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Languages
 *
 * @ORM\Table(name="languages")
 * @ORM\Entity
 */
class Languages
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="string", length=10, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="czech", type="string", length=10, nullable=true)
     */
    private $czech;

    /**
     * @var string|null
     *
     * @ORM\Column(name="english", type="string", length=10, nullable=true)
     */
    private $english;


}
