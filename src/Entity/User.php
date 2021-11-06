<?php

namespace App\Entity;

use App\Entity\Interfaces\RoleInterface;
use App\Entity\Interfaces\SoftDeleteableInterface;
use App\Entity\Interfaces\TimestampableInterface;
use App\Entity\Traits\SoftDeleteableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 *
 * @UniqueEntity(fields={"username"}, errorPath="username")
 * @UniqueEntity(fields={"apiKey"}, errorPath="apiKey")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 *
 * @ExclusionPolicy("all")
 */
class User implements UserInterface, TimestampableInterface, SoftDeleteableInterface
{
    const ROLE_DEFAULT = RoleInterface::ROLE_USER;

    use TimestampableTrait;
    use SoftDeleteableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private array $roles;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private ?string $password;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(groups={"registration", "api_create"})
     * @Assert\Length(min="6")
     */
    private ?string $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", unique=true)
     *
     * @Assert\NotBlank(groups={"registration", "api_create"})
     */
    private string $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="api_key", type="string", nullable=true, unique=true)
     */
    private ?string $apiKey;

    /**
     * @var ArrayCollection|UserGroup[]
     *
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_groups",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private Collection $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->roles = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): self
    {
        $this->plainPassword = null;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(?string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = self::ROLE_DEFAULT;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge(array_values(array_unique($roles)), $group->getRoles());
        }

        return array_unique($roles);
    }

    public function addRole(string $role): self
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return UserGroup[]|ArrayCollection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }
}
