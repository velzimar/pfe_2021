<?php

namespace App\Entity;

use App\Repository\WorkingHoursRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WorkingHoursRepository::class)
 */
class WorkingHours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="workingHours", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $business;

    /**
     * @ORM\Column(type="json")
     */
    private $hours = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusiness(): ?User
    {
        return $this->business;
    }

    public function setBusiness(User $business): self
    {
        $this->business = $business;

        return $this;
    }

    public function getHours(): ?array
    {
        return $this->hours;
    }

    public function setHours(array $hours): self
    {
        $this->hours = $hours;

        return $this;
    }
}
