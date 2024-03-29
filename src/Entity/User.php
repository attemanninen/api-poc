<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"public"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     *
     * @Groups({"public"})
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups({"public"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups({"public"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $apiKey;

    /**
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\OneToMany(targetEntity=TeamPermission::class, mappedBy="user")
     */
    private $teamPermissions;

    public function __construct(
        Company $company,
        string $email,
        string $plainPassword
    ) {
        $this->setCompany($company);
        $this->setEmail($email);
        $this->setPlainPassword($plainPassword);
        $this->teamPermissions = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPlainPassword(?string $plainPassword = null): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        return;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function addTeamPermission(TeamPermission $teamPermission): void
    {
        if (!$this->teamPermissions->contains($teamPermission)) {
            $this->teamPermissions->add($teamPermission);
            $teamPermission->setUser($this);
        }
    }

    public function getTeamPermissions(): Collection
    {
        return $this->teamPermissions;
    }

    public function removeTeamPermission(TeamPermission $teamPermission): void
    {
        if ($this->teamPermissions->removeElement($teamPermission)) {
            if ($teamPermission->getUser() === $this) {
                $teamPermission->setUser(null);
            }
        }
    }
}
