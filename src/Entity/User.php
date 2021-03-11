<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(fields={"email"}, message="Votre Email doit etre unique")
 * @Vich\Uploadable()
 */
class User implements UserInterface, Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private $id;

    /**
     * @ManyToOne(targetEntity="Category")
     * @JoinColumn(name="CategoryId", referencedColumnName="id")
     */
    private $CategoryId;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTimeInterface|null
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @var string|null
     * @Assert\NotBlank()
     * @Assert\Email()
     */

    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=8)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $businessName;

    /**
     * @ORM\Column(type="text", length=255)
     */
    private $businessDescription;

    /**
     * @ORM\OneToMany(targetEntity=ProductCategory::class, mappedBy="businessId")
     */
    private $productCategories;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="business")
     */
    private $products;


    //for deal
    /**
     * @ORM\OneToMany(targetEntity=DealCategory::class, mappedBy="businessId")
     */
    private $dealCategories;

    /**
     * @ORM\OneToMany(targetEntity=Deal::class, mappedBy="business")
     */
    private $deals;
    //end for deals




    /**
     * @ORM\Column(type="boolean", options={"default" : false}, nullable=false)
     */
    private $isActive;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="decimal", precision=10, scale=8)
     */
    private $latitude;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="decimal", precision=10, scale=7)
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="Receiver")
     */
    private $ReceivedNotifications;


    private $NotSeenReceivedNotifications;

    /**
     * @ORM\OneToOne(targetEntity=Delivery::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $delivery;

    public function __construct()
    {
        $this->productCategories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->dealCategories = new ArrayCollection();
        $this->deals = new ArrayCollection();
        $this->ReceivedNotifications = new ArrayCollection();
        $this->NotSeenReceivedNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    /**
     * @see UserInterface
     */
    public function getMainRole(): string
    {
        if (in_array("ROLE_SUPER", $this->roles)) {
            return "SuperAdmin";
        }else if(in_array("ROLE_ADMIN", $this->roles)){
            return "Admin";
        }else if(in_array("ROLE_SELLER", $this->roles)){
            return "Vendeur";
        }else{
            return "Invité";
        }
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param $role
     * @return bool
     * @see UserInterface
     */
    public function hasRole($role): bool
    {
        if (in_array($role, $this->roles)) {
            return true;
        }

        return false;
    }
    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }

        return $this;
    }
    public function removeRoles($role): self
    {
        if ($this->hasRole($role)) {
            unset($this->roles[array_search($role, $this->roles)]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }






    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    public function serialize(): ?string
    {
        return
            serialize([
                $this->id,
                $this->email,
                $this->password
            ]);
    }

    /**
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            )   = unserialize($serialized,['allowed_classes'=>false]);
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
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

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getBusinessDescription(): ?string
    {
        return $this->businessDescription;
    }

    public function setBusinessDescription(string $businessDescription): self
    {
        $this->businessDescription = $businessDescription;

        return $this;
    }



    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->CategoryId;
    }

    /**
     * @param mixed $CategoryId
     */
    public function setCategoryId($CategoryId): void
    {
        $this->CategoryId = $CategoryId;
    }

    /**
     * @return Collection|ProductCategory[]
     */
    public function getProductCategories(): Collection
    {
        return $this->productCategories;
    }

    public function addProductCategory(ProductCategory $productCategory): self
    {
        if (!$this->productCategories->contains($productCategory)) {
            $this->productCategories[] = $productCategory;
            $productCategory->setBusinessId($this);
        }

        return $this;
    }

    public function removeProductCategory(ProductCategory $productCategory): self
    {
        if ($this->productCategories->removeElement($productCategory)) {
            // set the owning side to null (unless already changed)
            if ($productCategory->getBusinessId() === $this) {
                $productCategory->setBusinessId(null);
            }
        }

        return $this;
    }


    // for deals
    /**
     * @return Collection|DealCategory[]
     */
    public function getDealCategories(): Collection
    {
        return $this->dealCategories;
    }

    public function addDealCategory(DealCategory $dealCategory): self
    {
        if (!$this->dealCategories->contains($dealCategory)) {
            $this->dealCategories[] = $dealCategory;
            $dealCategory->setBusinessId($this);
        }

        return $this;
    }

    public function removeDealCategory(DealCategory $dealCategory): self
    {
        if ($this->dealCategories->removeElement($dealCategory)) {
            // set the owning side to null (unless already changed)
            if ($dealCategory->getBusinessId() === $this) {
                $dealCategory->setBusinessId(null);
            }
        }

        return $this;
    }
    //end for deals
    public function __toString(): string
    {
        // to show the name of the Category in the select
        //return $this->nom;
        // to show the id of the Category in the select
         return strval($this->id);
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setBusiness($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBusiness() === $this) {
                $product->setBusiness(null);
            }
        }

        return $this;
    }

    // for deals

    /**
     * @return Collection|Deal[]
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    public function addDeal(Deal $deal): self
    {
        if (!$this->deals->contains($deal)) {
            $this->deals[] = $deal;
        $deal->setBusiness($this);
        }

        return $this;
    }

    public function removeDeal(Deal $deal): self
    {
        if ($this->deals->removeElement($deal)) {
            // set the owning side to null (unless already changed)
            if ($deal->getBusiness() === $this) {
                $deal->setBusiness(null);
            }
        }

        return $this;
    }

    //end for deals

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getReceivedNotifications(): Collection
    {
        return $this->ReceivedNotifications;
    }


    /**
     * @var $notification Notification
     * @return Collection|Notification[]
     */
    public function getNotSeenReceivedNotifications(): Collection
    {
        $this->NotSeenReceivedNotifications = new ArrayCollection();
        foreach ($this->ReceivedNotifications as $notification){
            if (!$notification->getSeen())
                $this->NotSeenReceivedNotifications[] = $notification;
        }

        return $this->NotSeenReceivedNotifications;
    }

    public function addReceivedNotification(Notification $receivedNotification): self
    {
        if (!$this->ReceivedNotifications->contains($receivedNotification)) {
            $this->ReceivedNotifications[] = $receivedNotification;
            $receivedNotification->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedNotification(Notification $receivedNotification): self
    {
        if ($this->ReceivedNotifications->removeElement($receivedNotification)) {
            // set the owning side to null (unless already changed)
            if ($receivedNotification->getReceiver() === $this) {
                $receivedNotification->setReceiver(null);
            }
        }

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        // unset the owning side of the relation if necessary
        if ($delivery === null && $this->delivery !== null) {
            $this->delivery->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($delivery !== null && $delivery->getUser() !== $this) {
            $delivery->setUser($this);
        }

        $this->delivery = $delivery;

        return $this;
    }


}
