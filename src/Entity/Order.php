<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $achternaam = null;

    #[ORM\Column(length: 100)]
    private ?string $voornaam = null;


    #[ORM\Column(length: 150)]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    private ?string $phoneNr = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time = null;

    #[ORM\OneToMany(mappedBy: 'order_item_id', targetEntity: OrderItems::class)]
    private Collection $orderItems;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn()]
    private ?User $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $ordered_at = null;

    #[ORM\Column(length: 255)]
    private ?string $straatnaam = null;

    #[ORM\Column(length: 30)]
    private ?string $huisnummer = null;

    #[ORM\Column(length: 50)]
    private ?string $postcode = null;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNr(): ?string
    {
        return $this->phoneNr;
    }

    public function setPhoneNr(string $phoneNr): static
    {
        $this->phoneNr = $phoneNr;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return Collection<int, OrderItems>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItems $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrderItemId($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItems $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getOrderItemId() === $this) {
                $orderItem->setOrderItemId(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getOrderedAt(): ?\DateTimeInterface
    {
        return $this->ordered_at;
    }

    public function setOrderedAt(\DateTimeInterface $ordered_at): static
    {
        $this->ordered_at = $ordered_at;

        return $this;
    }

    public function getAchternaam(): ?string
    {
        return $this->achternaam;
    }

    public function setAchternaam(string $achternaam): static
    {
        $this->achternaam = $achternaam;

        return $this;
    }

    public function getVoornaam(): ?string
    {
        return $this->voornaam;
    }

    public function setVoornaam(string $voornaam): static
    {
        $this->voornaam = $voornaam;

        return $this;
    }

    public function getStraatnaam(): ?string
    {
        return $this->straatnaam;
    }

    public function setStraatnaam(string $straatnaam): static
    {
        $this->straatnaam = $straatnaam;

        return $this;
    }

    public function getHuisnummer(): ?string
    {
        return $this->huisnummer;
    }

    public function setHuisnummer(string $huisnummer): static
    {
        $this->huisnummer = $huisnummer;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }
}
