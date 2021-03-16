<?php

namespace App\Entity;

use App\Repository\ServiceCalendarRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceCalendarRepository::class)
 */
class ServiceCalendar
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Service::class, inversedBy="serviceCalendar", cascade={"persist", "remove"})
     */
    private $service;


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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

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
