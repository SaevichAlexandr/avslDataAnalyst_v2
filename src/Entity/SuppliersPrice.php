<?php

namespace App\Entity;

use App\Repository\SuppliersPriceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SuppliersPriceRepository::class)
 */
class SuppliersPrice
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="suppliersPrice")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity=OfferData::class, inversedBy="suppliersPrices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $offerData;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    public function __construct(Supplier $supplier, OfferData $offerData)
    {
        $this->supplier = $supplier;
        $this->offerData = $offerData;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function getOfferData(): ?OfferData
    {
        return $this->offerData;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
