<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(
 *    name="service",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_servicecategory_service", columns={"nom", "category_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 * @Vich\Uploadable()
 */
class Service
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="service_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;


    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=3)
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     */
    private $qtt;

    /**
     * @ORM\ManyToOne(targetEntity=ServiceCategory::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     */
    private $business;



    /*
     * @ORM\OneToMany(targetEntity=ServiceOptions::class, mappedBy="service")
     */
   // private $serviceOptions;

    /*
     * @ORM\OneToOne(targetEntity=ServiceCalendar::class, mappedBy="service", cascade={"persist", "remove"})
     */
    //private $serviceCalendar;



    public function __construct()
    {
        $this->serviceOptions = new ArrayCollection();
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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQtt(): ?int
    {
        return $this->qtt;
    }

    public function setQtt(int $qtt): self
    {
        $this->qtt = $qtt;

        return $this;
    }

    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }

    public function setCategory(?ServiceCategory $category): self
    {
        $this->category = $category;

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
    public function __toString(): string
    {
        // to show the name of the Category in the select
        //return $this->id;
        // to show the id of the Category in the select
        return strval($this->id);
    }


    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if($this->imageFile instanceof UploadedFile){
            $this->updatedAt = new DateTime('now');
        }
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }



    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /*
     * @return Collection|ServiceOptions[]
     */
    /*
    public function getServiceOptions(): Collection
    {
        return $this->serviceOptions;
    }

    public function addServiceOption(ServiceOptions $serviceOption): self
    {
        if (!$this->serviceOptions->contains($serviceOption)) {
            $this->serviceOptions[] = $serviceOption;
            $serviceOption->setServiceId($this);
        }

        return $this;
    }

    public function removeServiceOption(ServiceOptions $serviceOption): self
    {
        if ($this->serviceOptions->removeElement($serviceOption)) {
            // set the owning side to null (unless already changed)
            if ($serviceOption->getServiceId() === $this) {
                $serviceOption->setServiceId(null);
            }
        }

        return $this;
    }

    public function getServiceCalendar(): ?ServiceCalendar
    {
        return $this->serviceCalendar;
    }

    public function setServiceCalendar(?ServiceCalendar $serviceCalendar): self
    {
        // unset the owning side of the relation if necessary
        if ($serviceCalendar === null && $this->serviceCalendar !== null) {
            $this->serviceCalendar->setService(null);
        }

        // set the owning side of the relation if necessary
        if ($serviceCalendar !== null && $serviceCalendar->getService() !== $this) {
            $serviceCalendar->setService($this);
        }

        $this->serviceCalendar = $serviceCalendar;

        return $this;
    }
    */

}
