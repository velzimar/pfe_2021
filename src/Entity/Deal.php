<?php

namespace App\Entity;

use App\Repository\DealRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(
 *    name="deal",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_dealcategory_deal", columns={"nom", "category_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=DealRepository::class)
 * @Vich\Uploadable()
 * @UniqueEntity("nom")
 */

class Deal
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
     * @Vich\UploadableField(mapping="deal_image", fileNameProperty="filename")
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
     * @Assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     * @ORM\Column(type="decimal", precision=10, scale=3)
     */
    private $prix;

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     * @ORM\Column(type="integer")
     */
    private $qtt;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=DealCategory::class, inversedBy="deals")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $business;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_add;


    /**
     * @Assert\NotBlank
     * @Assert\Positive
     * @ORM\Column(type="decimal", precision=10, scale=3, nullable=false)
     */
    private $real_price;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="dateinterval", nullable=false)
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end_date;


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

    public function getCategory(): ?DealCategory
    {
        return $this->category;
    }

    public function setCategory(?DealCategory $category): self
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

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(?\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }





    public function getRealPrice(): ?string
    {
        return $this->real_price;
    }

    public function setRealPrice(?string $real_price): self
    {
        $this->real_price = $real_price;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    /**
     * @param \DateTimeInterface|null $date
     * @return $this
     */
    public function setEndDate(?\DateTimeInterface $date): self
    {
        $this->end_date = $date;
        return $this;
    }
}
