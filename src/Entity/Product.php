<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(
 *    name="product",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_productcategory_product", columns={"nom", "category_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Vich\Uploadable()
 */
class Product
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
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="filename")
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
     * @ORM\ManyToOne(targetEntity=ProductCategory::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $business;

    /**
     * @ORM\OneToMany(targetEntity=ProductOptions::class, mappedBy="product")
     */
    private $productOptions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    /**
     * @ORM\OneToMany(targetEntity=SubOrderProduct::class, mappedBy="product")
     */
    private $subOrderProducts;

    public function __construct()
    {
        $this->productOptions = new ArrayCollection();
        $this->subOrderProducts = new ArrayCollection();
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

    public function getCategory(): ?ProductCategory
    {
        return $this->category;
    }

    public function setCategory(?ProductCategory $category): self
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

    /**
     * @return Collection|ProductOptions[]
     */
    public function getProductOptions(): Collection
    {
        return $this->productOptions;
    }

    public function addProductOption(ProductOptions $productOption): self
    {
        if (!$this->productOptions->contains($productOption)) {
            $this->productOptions[] = $productOption;
            $productOption->setProduct($this);
        }

        return $this;
    }

    public function removeProductOption(ProductOptions $productOption): self
    {
        if ($this->productOptions->removeElement($productOption)) {
            // set the owning side to null (unless already changed)
            if ($productOption->getProduct() === $this) {
                $productOption->setProduct(null);
            }
        }

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

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
            $subOrderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeSubOrderProduct(SubOrderProduct $subOrderProduct): self
    {
        if ($this->subOrderProducts->removeElement($subOrderProduct)) {
            // set the owning side to null (unless already changed)
            if ($subOrderProduct->getProduct() === $this) {
                $subOrderProduct->setProduct(null);
            }
        }

        return $this;
    }



}
