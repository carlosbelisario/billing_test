<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice
{
    const STATUS_ACTIVE = 'active';

    const STATUS_DISABLED = 'disabled';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $number;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Client", inversedBy="bill")
     * @var Client $client
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="bill")
     * @var Item $item
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="invoice")
     */
    private $company;

    public function __construct()
    {
        $this->item = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function calculateSubtotal()
    {
        foreach ($this->getItem() as $item) {
            $this->subtotal += $item->getPrice();
        }

        return $this;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function calculateIva()
    {
        $this->iva = $this->subtotal * 0.21;

        return $this;
    }

    public function getTotal() : float
    {
        return $this->subtotal + $this->iva;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItem(): Collection
    {
        return $this->item;
    }

    public function addItem(Item $item): self
    {
        if (!$this->item->contains($item)) {
            $this->item[] = $item;
            $item->setBill($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->item->contains($item)) {
            $this->item->removeElement($item);
            // set the owning side to null (unless already changed)
            if ($item->getBill() === $this) {
                $item->setBill(null);
            }
        }

        return $this;
    }

    public function setSubtotal(?float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function setIva(?float $iva): self
    {
        $this->iva = $iva;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
