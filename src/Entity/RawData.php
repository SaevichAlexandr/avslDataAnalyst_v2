<?php

namespace App\Entity;

use App\Repository\RawDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RawDataRepository::class)
 */
class RawData
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SearchParams::class, inversedBy="rawData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $searchParams;

    /**
     * @ORM\Column(type="text")
     */
    private $offerText;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isParsed = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchParams(): ?SearchParams
    {
        return $this->searchParams;
    }

    public function setSearchParams(?SearchParams $searchParams): self
    {
        $this->searchParams = $searchParams;

        return $this;
    }

    public function getOfferText(): ?string
    {
        return $this->offerText;
    }

    public function setOfferText(string $offerText): self
    {
        $this->offerText = $offerText;

        return $this;
    }

    public function getIsParsed()
    {
        $this->isParsed;
    }

    public function setIsParsed(bool $isParsed): self
    {
        $this->isParsed = $isParsed;

        return $this;
    }
}
