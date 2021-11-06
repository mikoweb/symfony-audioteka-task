<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Entity;

use App\Entity\Interfaces\SoftDeleteableInterface;
use App\Entity\Interfaces\TimestampableInterface;
use App\Entity\Traits\SoftDeleteableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\UuidV4;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 *
 * @UniqueEntity(fields={"slug"}, errorPath="slug")
 * @UniqueEntity(fields={"id"}, errorPath="id")
 *
 * @ExclusionPolicy("all")
 */
class Product implements TimestampableInterface, SoftDeleteableInterface
{
    use TimestampableTrait;
    use SoftDeleteableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", name="id", unique=true)
     *
     * @Expose
     */
    private string $id;

    /**
     * @ORM\Column(type="string", name="name", nullable=false, length=255)
     *
     * @Expose
     */
    private string $name;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", name="slug", unique=true, nullable=false, length=255)
     *
     * @Expose
     */
    private string $slug;

    /**
     * @ORM\Column(name="price", type="decimal", precision=8, scale=2, nullable=false)
     *
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}
