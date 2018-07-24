<?php

namespace Zitkino\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Screenings
 *
 * @ORM\Table(name="screenings", indexes={@ORM\Index(name="movie", columns={"movie"}), @ORM\Index(name="cinema", columns={"cinema"}), @ORM\Index(name="language", columns={"dubbing"}), @ORM\Index(name="subtitles", columns={"subtitles"})})
 * @ORM\Entity
 */
class Screenings
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
     * @var enum|null
     *
     * @ORM\Column(name="type", type="enum", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var int|null
     *
     * @ORM\Column(name="price", type="integer", nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link", type="string", length=1000, nullable=true)
     */
    private $link;

    /**
     * @var \Zitkino\Entities\Movie
     *
     * @ORM\ManyToOne(targetEntity="Zitkino\Entities\Movies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movie", referencedColumnName="id")
     * })
     */
    private $movie;

    /**
     * @var \Zitkino\Entities\Cinema
     *
     * @ORM\ManyToOne(targetEntity="Zitkino\Entities\Cinemas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cinema", referencedColumnName="id")
     * })
     */
    private $cinema;

    /**
     * @var \Zitkino\Entities\Languages
     *
     * @ORM\ManyToOne(targetEntity="Zitkino\Entities\Languages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dubbing", referencedColumnName="id")
     * })
     */
    private $dubbing;

    /**
     * @var \Zitkino\Entities\Languages
     *
     * @ORM\ManyToOne(targetEntity="Zitkino\Entities\Languages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subtitles", referencedColumnName="id")
     * })
     */
    private $subtitles;


}
