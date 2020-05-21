<?php

namespace App\Entity;

use App\Repository\SearchParamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SearchParamsRepository::class)
 */
class SearchParams
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $departurePoint;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $arrivalPoint;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $toDepartureDay;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $toDepartureMonth;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $fromDepartureDay;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private $fromDepartureMonth;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $reservationClass;

    /**
     * @ORM\Column(type="integer")
     */
    private $adults;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $children;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $infants;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $showMoreClicks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isChecked = false;

    /**
     * @ORM\OneToMany(targetEntity=RawData::class, mappedBy="searchParamsId", orphanRemoval=true)
     */
    private $rawData;

    public function __construct()
    {
        $this->rawData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeparturePoint(): ?string
    {
        return $this->departurePoint;
    }

    public function setDeparturePoint(string $departurePoint): self
    {
        $this->departurePoint = $departurePoint;

        return $this;
    }

    public function getArrivalPoint(): ?string
    {
        return $this->arrivalPoint;
    }

    public function setArrivalPoint(string $arrivalPoint): self
    {
        $this->arrivalPoint = $arrivalPoint;

        return $this;
    }

    public function getToDepartureDay(): ?string
    {
        return $this->toDepartureDay;
    }

    public function setToDepartureDay(string $toDepartureDay): self
    {
        $this->toDepartureDay = $toDepartureDay;

        return $this;
    }

    public function getToDepartureMonth(): ?string
    {
        return $this->toDepartureMonth;
    }

    public function setToDepartureMonth(string $toDepartureMonth): self
    {
        $this->toDepartureMonth = $toDepartureMonth;

        return $this;
    }

    public function getFromDepartureDay(): ?string
    {
        return $this->fromDepartureDay;
    }

    public function setFromDepartureDay(?string $fromDepartureDay): self
    {
        $this->fromDepartureDay = $fromDepartureDay;

        return $this;
    }

    public function getFromDepartureMonth(): ?string
    {
        return $this->fromDepartureMonth;
    }

    public function setFromDepartureMonth(?string $fromDepartureMonth): self
    {
        $this->fromDepartureMonth = $fromDepartureMonth;

        return $this;
    }

    public function getReservationClass(): ?string
    {
        return $this->reservationClass;
    }

    public function setReservationClass(?string $reservationClass): self
    {
        $this->reservationClass = $reservationClass;

        return $this;
    }

    public function getAdults(): ?int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): self
    {
        $this->adults = $adults;

        return $this;
    }

    public function getChildren(): ?int
    {
        return $this->children;
    }

    public function setChildren(?int $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getInfants(): ?int
    {
        return $this->infants;
    }

    public function setInfants(?int $infants): self
    {
        $this->infants = $infants;

        return $this;
    }

    public function getShowMoreClicks(): ?int
    {
        return $this->showMoreClicks;
    }

    public function setShowMoreClicks(?int $showMoreClicks): self
    {
        $this->showMoreClicks = $showMoreClicks;

        return $this;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setIsChecked(bool $isChecked): self
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    public function getIsChecked(): ?bool
    {
        return $this->isChecked;
    }

    /**
     * @return Collection|RawData[]
     */
    public function getRawData(): Collection
    {
        return $this->rawData;
    }

    public function addRawData(RawData $rawData): self
    {
        if (!$this->rawData->contains($rawData)) {
            $this->rawData[] = $rawData;
            $rawData->setSearchParamsId($this);
        }

        return $this;
    }

    public function removeRawData(RawData $rawData): self
    {
        if ($this->rawData->contains($rawData)) {
            $this->rawData->removeElement($rawData);
            // set the owning side to null (unless already changed)
            if ($rawData->getSearchParamsId() === $this) {
                $rawData->setSearchParamsId(null);
            }
        }

        return $this;
    }
}
