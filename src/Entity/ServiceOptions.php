<?php

namespace App\Entity;

use App\Repository\ServiceOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    name="service_options",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_serviceId_nom", columns={"nom", "service_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ServiceOptionsRepository::class)
 */
class ServiceOptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="json")
     */
    private $choices = [];

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="serviceOptions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $service;

    /**
     * @ORM\Column(type="integer")
     */
    private $NbMaxSelected;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getChoices(): ?array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
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

    public function getNbMaxSelected(): ?int
    {
        return $this->NbMaxSelected;
    }

    public function setNbMaxSelected(int $NbMaxSelected): self
    {
        $this->NbMaxSelected = $NbMaxSelected;

        return $this;
    }
}
