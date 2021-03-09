<?php

namespace App\Entity;

use App\Repository\ProductOptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    name="product_options",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="unique_productId_nom", columns={"nom", "product_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass=ProductOptionsRepository::class)
 */
class ProductOptions
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
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productOptions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $product;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
