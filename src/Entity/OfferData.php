<?php

namespace App\Entity;

use App\Repository\OfferDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferDataRepository::class)
 */
class OfferData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=RawData::class, inversedBy="offerData", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $rawData;

    /**
     * @ORM\Column(type="float")
     */
    private $akassaPrice;

    /**
     * @ORM\Column(type="float")
     */
    private $buttonPrice;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $akassaHref;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $baggage;

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
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $transferTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=OfferDataFlight::class, mappedBy="offerDataId", orphanRemoval=true)
     */
    private $offerDataFlights;

    /**
     * @ORM\OneToMany(targetEntity=SuppliersPrice::class, mappedBy="offerData", orphanRemoval=true)
     */
    private $suppliersPrices;

    public function __construct()
    {
        $this->offerDataFlights = new ArrayCollection();
        $this->suppliersPrices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRawData(): ?RawData
    {
        return $this->rawData;
    }

    public function setRawData(RawData $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getAkassaPrice(): ?float
    {
        return $this->akassaPrice;
    }

    public function setAkassaPrice(float $akassaPrice): self
    {
        $this->akassaPrice = $akassaPrice;

        return $this;
    }

    public function getButtonPrice(): ?float
    {
        return $this->buttonPrice;
    }

    public function setButtonPrice(float $buttonPrice): self
    {
        $this->buttonPrice = $buttonPrice;

        return $this;
    }

    public function getAkassaHref(): ?string
    {
        return $this->akassaHref;
    }

    public function setAkassaHref(?string $akassaHref): self
    {
        $this->akassaHref = $akassaHref;

        return $this;
    }

    public function getBaggage(): ?string
    {
        return $this->baggage;
    }

    public function setBaggage(string $baggage): self
    {
        $this->baggage = $baggage;

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

    public function getTransferTime(): ?string
    {
        return $this->transferTime;
    }

    public function setTransferTime(?string $transferTime): self
    {
        $this->transferTime = $transferTime;

        return $this;
    }
    // TODO: по-хорошему, таймштампы надо бы переписать как-то так
    // https://stackoverflow.com/questions/11504997/symfony2-datetime-best-way-to-store-timestamps
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
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
            $offerDataFlight->setOfferDataId($this);
        }

        return $this;
    }

    public function removeOfferDataFlight(OfferDataFlight $offerDataFlight): self
    {
        if ($this->offerDataFlights->contains($offerDataFlight)) {
            $this->offerDataFlights->removeElement($offerDataFlight);
            // set the owning side to null (unless already changed)
            if ($offerDataFlight->getOfferDataId() === $this) {
                $offerDataFlight->setOfferDataId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SuppliersPrice[]
     */
    public function getSuppliersPrices(): Collection
    {
        return $this->suppliersPrices;
    }

    public function addSuppliersPrice(SuppliersPrice $suppliersPrice): self
    {
        if (!$this->suppliersPrices->contains($suppliersPrice)) {
            $this->suppliersPrices[] = $suppliersPrice;
            $suppliersPrice->setOfferData($this);
        }

        return $this;
    }

    public function removeSuppliersPrice(SuppliersPrice $suppliersPrice): self
    {
        if ($this->suppliersPrices->contains($suppliersPrice)) {
            $this->suppliersPrices->removeElement($suppliersPrice);
            // set the owning side to null (unless already changed)
            if ($suppliersPrice->getOfferData() === $this) {
                $suppliersPrice->setOfferData(null);
            }
        }

        return $this;
    }
}
