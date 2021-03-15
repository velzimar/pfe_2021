<?php

namespace App\Entity;

use App\Repository\ProductCalendarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductCalendarRepository::class)
 */
class ProductCalendar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="productCalendar", cascade={"persist", "remove"})
     */
    private $product;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="json")
     */
    private $slots = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }



    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSlots(): ?array
    {
        return $this->slots;
    }

    public function setSlots(array $slots): self
    {
        $this->slots = $slots;

        return $this;
    }
}
