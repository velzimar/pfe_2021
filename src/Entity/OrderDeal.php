<?php

namespace App\Entity;

use App\Repository\OrderDealRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    name="order_deal",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_user_deal", columns={"user_id", "deal_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=OrderDealRepository::class)
 */
class OrderDeal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orderDeals")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="IncomingOrderDeals")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $business;

    /**
     * @ORM\ManyToOne(targetEntity=Deal::class, inversedBy="orderDeals")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $deal;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $code;


    /**
     * @ORM\Column(type="boolean", options={"default" : false}, nullable=false)
     */
    private $isUsed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setIsUsed(bool $isActive): self
    {
        $this->isUsed = $isActive;

        return $this;
    }


    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBusiness(): ?User
    {
        return $this->business;
    }

    public function setBusiness(?User $user): self
    {
        $this->business = $user;

        return $this;
    }

    public function getDeal(): ?Deal
    {
        return $this->deal;
    }

    public function setDeal(?Deal $deal): self
    {
        $this->deal = $deal;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
