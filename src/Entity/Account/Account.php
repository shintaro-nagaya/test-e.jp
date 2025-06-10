<?php

namespace App\Entity\Account;

use App\Entity\Traits\ModifiedTimeTrait;
use App\Repository\Account\AccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: "account")]
#[ORM\HasLifecycleCallbacks()]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
#[UniqueEntity("email", message: "このメールアドレスは既に登録されています")]
class Account implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SoftDeleteableEntity;
    use ModifiedTimeTrait;

    public function __toString(): string
    {
        return $this->name;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password;

    #[ORM\Column(type: 'string', length: 48)]
    private ?string $name;

    #[ORM\Column]
    private ?bool $adminLightMode = false;

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
    public function getUserIdentifier(): string
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasAdmin(): bool
    {
        return in_array("ROLE_ADMIN", $this->roles, true);
    }
    public function hasSuperAdmin(): bool
    {
        return in_array("ROLE_SUPER_ADMIN", $this->roles, true);
    }

    public function getRoleNames(): array
    {
        $roles = [];
        if($this->hasAdmin()) {
            $roles[] = "管理画面一般";
        }
        if($this->hasSuperAdmin()) {
            $roles[] = "管理画面アカウント権限";
        }
        return $roles;
    }

    public function isAdminLightMode(): bool
    {
        return $this->adminLightMode;
    }

    public function setAdminLightMode(bool $adminLightMode): self
    {
        $this->adminLightMode = $adminLightMode;

        return $this;
    }
}
