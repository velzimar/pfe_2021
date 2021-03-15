<?php

namespace App\Entity;

use App\Repository\ServiceCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    name="service_category",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_businessId_nom", columns={"nom", "business_id_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ServiceCategoryRepository::class)
 */
class ServiceCategory
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="serviceCategories")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $businessId;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="category")
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBusinessId(): string
    {
        return $this->businessId;
    }

    public function setBusinessId(User $businessId): self
    {
        $this->businessId = $businessId;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCategory() === $this) {
                $service->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        // to show the name of the Category in the select
        return $this->nom;
        // to show the id of the Category in the select
        //return strval($this->id);
    }
}
