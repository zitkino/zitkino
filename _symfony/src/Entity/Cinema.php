<?php

namespace App\Entity;

use App\Repository\CinemaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cinema
 *
 * @ORM\Table(name="zk_cinemas", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"}), @ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="type", columns={"type"})})
 * @ORM\Entity(repositoryClass=CinemaRepository::class)
 */
class Cinema
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
     * @var string
     * @ORM\Column(name="code", type="string", length=20, nullable=false)
     */
    private $code;

    /**
     * @var CinemaType
     * @ORM\ManyToOne(targetEntity=CinemaType::class)
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
     * })
     */
    private $type;

    /**
     * @var string|null
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", length=255, nullable=false, options={"default"="Brno"})
     */
    private $city = 'Brno';

    /**
     * @var string|null
     * @ORM\Column(name="phone", type="string", length=100, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     * @ORM\Column(name="url", type="string", length=1000, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     * @ORM\Column(name="gmaps", type="string", length=1000, nullable=true)
     */
    private $gmaps;

    /**
     * @var string|null
     * @ORM\Column(name="programme", type="string", length=255, nullable=true)
     */
    private $programme;

    /**
     * @var string|null
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @var string|null
     * @ORM\Column(name="googlePlus", type="string", length=255, nullable=true)
     */
    private $googlePlus;

    /**
     * @var string|null
     * @ORM\Column(name="instagram", type="string", length=255, nullable=true)
     */
    private $instagram;

    /**
     * @var string|null
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="active_since", type="date", nullable=true)
     */
    private $activeSince;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="active_until", type="date", nullable=true)
     */
    private $activeUntil;

    /**
     * @var bool
     * @ORM\Column(name="parsable", type="boolean", options={"default": 0}, nullable=false)
     */
    private $parsable;

    /**
     * @var string|null
     * @ORM\Column(name="parsing", type="string", length=255, nullable=true)
     */
    private $parsing;

    /**
     * @var \DateTime|null
     * @ORM\Column(name="parsed", type="datetime", nullable=true)
     */
    private $parsed;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Screening::class, mappedBy="cinema", cascade={"persist", "remove"})
     */
    private $screenings;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity=Place::class, mappedBy="cinema")
     */
    private $places;

    public function __construct(string $code)
    {
        $this->code = $code;
        $this->name = $code;
        $this->screenings = new ArrayCollection();
        $this->places = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCode();
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getType(): ?CinemaType
    {
        return $this->type;
    }

    public function setType(?CinemaType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getGmaps(): ?string
    {
        return $this->gmaps;
    }

    public function setGmaps(?string $gmaps): self
    {
        $this->gmaps = $gmaps;
        return $this;
    }

    public function getProgramme(): ?string
    {
        return $this->programme;
    }

    public function setProgramme(?string $programme): self
    {
        $this->programme = $programme;
        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;
        return $this;
    }

    public function getGooglePlus(): ?string
    {
        return $this->googlePlus;
    }

    public function setGooglePlus(?string $googlePlus): self
    {
        $this->googlePlus = $googlePlus;
        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;
        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;
        return $this;
    }

    public function getActiveSince(): ?\DateTime
    {
        return $this->activeSince;
    }

    public function setActiveSince(?\DateTime $activeSince): self
    {
        $this->activeSince = $activeSince;
        return $this;
    }

    public function getActiveUntil(): ?\DateTime
    {
        return $this->activeUntil;
    }

    public function setActiveUntil(?\DateTime $activeUntil): self
    {
        $this->activeUntil = $activeUntil;
        return $this;
    }

    public function isParsable(): bool
    {
        return $this->parsable;
    }

    public function setParsable(bool $parsable): self
    {
        $this->parsable = $parsable;
        return $this;
    }

    public function getParsing(): ?string
    {
        return $this->parsing;
    }

    public function setParsing(?string $parsing): self
    {
        $this->parsing = $parsing;
        return $this;
    }

    public function getParsed(): ?\DateTime
    {
        return $this->parsed;
    }

    public function setParsed(?\DateTime $parsed): self
    {
        $this->parsed = $parsed;
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
            $screening->setCinema($this);
        }

        return $this;
    }

    public function removeScreening(Screening $screening): self
    {
        if ($this->screenings->removeElement($screening)) {
            // set the owning side to null (unless already changed)
            if ($screening->getCinema() === $this) {
                $screening->setCinema(null);
            }
        }

        return $this;
    }

    public function hasScreenings(): bool
    {
        return !$this->screenings->isEmpty();
    }

    /**
     * @return Collection|Place[]
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places[] = $place;
            $place->setCinema($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getCinema() === $this) {
                $place->setCinema(null);
            }
        }

        return $this;
    }
}
