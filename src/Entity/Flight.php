<?php

namespace App\Entity;

use App\Repository\FlightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlightRepository::class)
 */
class Flight
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $flightNumber;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $flightTime;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $airline;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $departurePoint;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $arrivalPoint;

    /**
     * @ORM\Column(type="datetime")
     */
    private $departureDatetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $arrivalDatetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=OfferDataFlight::class, mappedBy="flightId", orphanRemoval=true)
     */
    private $offerDataFlights;

    public function __construct()
    {
        $this->offerDataFlights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(string $flightNumber): self
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getFlightTime(): ?string
    {
        return $this->flightTime;
    }

    public function setFlightTime(string $flightTime): self
    {
        $this->flightTime = $flightTime;

        return $this;
    }

    public function getAirline(): ?string
    {
        return $this->airline;
    }

    public function setAirline(string $airline): self
    {
        $this->airline = $airline;

        return $this;
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

    public function getDepartureDatetime(): ?\DateTimeInterface
    {
        return $this->departureDatetime;
    }

    public function setDepartureDatetime(\DateTimeInterface $departureDatetime): self
    {
        $this->departureDatetime = $departureDatetime;

        return $this;
    }

    public function getArrivalDatetime(): ?\DateTimeInterface
    {
        return $this->arrivalDatetime;
    }

    public function setArrivalDatetime(\DateTimeInterface $arrivalDatetime): self
    {
        $this->arrivalDatetime = $arrivalDatetime;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|OfferDataFlight[]
     */
    public function getOfferDataFlights(): Collection
    {
        return $this->offerDataFlights;
    }

    public function addOfferDataFlight(OfferDataFlight $offerDataFlight): self
    {
        if (!$this->offerDataFlights->contains($offerDataFlight)) {
            $this->offerDataFlights[] = $offerDataFlight;
            $offerDataFlight->setFlightId($this);
        }

        return $this;
    }

    public function removeOfferDataFlight(OfferDataFlight $offerDataFlight): self
    {
        if ($this->offerDataFlights->contains($offerDataFlight)) {
            $this->offerDataFlights->removeElement($offerDataFlight);
            // set the owning side to null (unless already changed)
            if ($offerDataFlight->getFlightId() === $this) {
                $offerDataFlight->setFlightId(null);
            }
        }

        return $this;
    }
}
