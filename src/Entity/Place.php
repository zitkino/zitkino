<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Place
 *
 * @ORM\Table(name="zk_places")
 * @ORM\Entity(repositoryClass=\App\Repository\PlaceRepository::class)
 */
class Place
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     * @ORM\Column(name="link", type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @var Cinema
     * @ORM\ManyToOne(targetEntity=Cinema::class, inversedBy="places")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cinema", referencedColumnName="id", nullable=false)
     * })
     */
    private $cinema;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Screening::class, mappedBy="place")
     */
    private $screenings;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->screenings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): self
    {
        $this->cinema = $cinema;
        return $this;
    }

    /**
     * @return Collection|Screening[]
     */
    public function getScreenings(): Collection
    {
        return $this->screenings;
    }

    public function addScreening(Screening $screening): self
    {
        if (!$this->screenings->contains($screening)) {
            $this->screenings[] = $screening;
            $screening->setPlace($this);
        }

        return $this;
    }

    public function removeScreening(Screening $screening): self
    {
        if ($this->screenings->removeElement($screening)) {
            // set the owning side to null (unless already changed)
            if ($screening->getPlace() === $this) {
                $screening->setPlace(null);
            }
        }

        return $this;
    }
}
