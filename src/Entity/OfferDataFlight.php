<?php

namespace App\Entity;

use App\Repository\OfferDataFlightRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferDataFlightRepository::class)
 */
class OfferDataFlight
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=OfferData::class, inversedBy="offerDataFlights")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offerData;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Flight::class, inversedBy="offerDataFlights")
     * @ORM\JoinColumn(nullable=false)
     */
    private $flight;

    public function __construct(OfferData $offerData, Flight $flight)
    {
        $this->offerData = $offerData;
        $this->flight = $flight;
    }

    public function getOfferData(): ?OfferData
    {
        return $this->offerData;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }
}
