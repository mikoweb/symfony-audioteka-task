<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Entity;

use App\Entity\Interfaces\TimestampableInterface;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 * @ORM\Table(name="carts")
 *
 * @ExclusionPolicy("all")
 */
class Cart implements TimestampableInterface
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotNull()
     */
    private User $user;

    /**
     * @var ArrayCollection|CartItem[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="cart")
     */
    private array|ArrayCollection|Collection $items;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->items = new ArrayCollection();
    }

    public function getId(): UuidV4|string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return CartItem[]|ArrayCollection|Collection
     */
    public function getItems(): array|ArrayCollection|Collection
    {
        return $this->items;
    }
}
