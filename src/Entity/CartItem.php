<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Entity;

use App\Entity\Interfaces\TimestampableInterface;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartItemRepository")
 * @ORM\Table(name="cart_items")
 *
 * @ExclusionPolicy("all")
 */
class CartItem implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", name="id", unique=true)
     *
     * @Expose
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cart", inversedBy="items")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @Expose
     */
    private Cart $cart;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @Expose
     */
    private Product $product;

    /**
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     *
     * @Assert\Positive()
     *
     * @Expose
     */
    private int $quantity;

    /**
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2, nullable=false)
     *
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(value="0");
     *
     * @Expose
     */
    private float $price;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): UuidV4|string
    {
        return $this->id;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function increaseQuantity(int $quantity): void
    {
        $this->quantity += $quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @VirtualProperty
     * @SerializedName("sumPrice")
     */
    public function sumPrice(): float
    {
        return $this->getPrice() * $this->getQuantity();
    }
}
