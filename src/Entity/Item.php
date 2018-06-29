<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ItemRepository")
 */
class Item
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $length;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Bill", inversedBy="item")
     * @var Invoice $bill
     */
    private $invoice;

    public function getId()
    {
        return $this->id;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBill(): ?Invoice
    {
        return $this->invoice;
    }

    public function setBill(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public static function createFromArray(array $data)
    {
        $instance = new static();
        foreach ($data as $field => $value) {
            $instance->$field = $value;
        }

        return $instance;
    }

    public function getInvoice(): ?Bill
    {
        return $this->invoice;
    }

    public function setInvoice(?Bill $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

}
