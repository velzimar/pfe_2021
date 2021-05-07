<?php

namespace App\Entity;

use App\Repository\OrderProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderProductRepository::class)
 */
class OrderProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orderProducts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="IncomingOrderProducts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $business;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $delivery;

    /**
     * @ORM\Column(type="datetime")
     */
    private $orderDate;

    /**
     * @ORM\Column(type="string", options={"default" : "en attente"}, length=20)
     */
    private $status;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=SubOrderProduct::class, mappedBy="orderProduct")
     */
    private $subOrderProducts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $modifyDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $seen;

    public function __construct()
    {
        $this->subOrderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getBusiness(): ?User
    {
        return $this->business;
    }

    public function setBusiness(?User $business): self
    {
        $this->business = $business;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(?string $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;

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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|SubOrderProduct[]
     */
    public function getSubOrderProducts(): Collection
    {
        return $this->subOrderProducts;
    }

    public function addSubOrderProduct(SubOrderProduct $subOrderProduct): self
    {
        if (!$this->subOrderProducts->contains($subOrderProduct)) {
            $this->subOrderProducts[] = $subOrderProduct;
            $subOrderProduct->setOrderProduct($this);
        }

        return $this;
    }

    public function removeSubOrderProduct(SubOrderProduct $subOrderProduct): self
    {
        if ($this->subOrderProducts->removeElement($subOrderProduct)) {
            // set the owning side to null (unless already changed)
            if ($subOrderProduct->getOrderProduct() === $this) {
                $subOrderProduct->setOrderProduct(null);
            }
        }

        return $this;
    }

    public function getModifyDate(): ?\DateTimeInterface
    {
        return $this->modifyDate;
    }

    public function setModifyDate(\DateTimeInterface $modifyDate): self
    {
        $this->modifyDate = $modifyDate;

        return $this;
    }

    public function getSeen(): ?bool
    {
        return $this->seen;
    }

    public function setSeen(?bool $seen): self
    {
        $this->seen = $seen;

        return $this;
    }
}
