<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=SuppliersPrice::class, mappedBy="supplier", orphanRemoval=true)
     */
    private $suppliersPrice;

    public function __construct()
    {
        $this->suppliersPrice = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|SuppliersPrice[]
     */
    public function getSuppliersPrice(): Collection
    {
        return $this->suppliersPrice;
    }

    public function addSuppliersPrice(SuppliersPrice $suppliersPrice): self
    {
        if (!$this->suppliersPrice->contains($suppliersPrice)) {
            $this->suppliersPrice[] = $suppliersPrice;
            $suppliersPrice->setSupplier($this);
        }

        return $this;
    }

    public function removeSuppliersPrice(SuppliersPrice $suppliersPrice): self
    {
        if ($this->suppliersPrice->contains($suppliersPrice)) {
            $this->suppliersPrice->removeElement($suppliersPrice);
            // set the owning side to null (unless already changed)
            if ($suppliersPrice->getSupplier() === $this) {
                $suppliersPrice->setSupplier(null);
            }
        }

        return $this;
    }
}
