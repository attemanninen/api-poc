<?php

namespace App\Entity;

use App\Repository\GroupRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupRoleRepository::class)
 */
class GroupRole
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
     * @ORM\ManyToOne(
     *   targetEntity=User::class,
     *   inversedBy="groupRoles"
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"public"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(
     *  targetEntity=Group::class
     * )
     * @ORM\JoinColumn(
     *  name="`group`",
     *  referencedColumnName="id",
     *  nullable=false
     * )
     *
     * @Groups({"public"})
     */
    private $group;

    /**
     * @ORM\Column(type="array")
     *
     * @Groups({"public"})
     */
    private $roles;

    public function __construct(User $user, Group $group, array $roles)
    {
        $this->setUser($user);
        $this->setGroup($group);
        $this->setRoles($roles);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = array_values($roles);
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function removeRole(string $role): void
    {
        $key = array_search($role, $this->roles);
        if ($key !== false) {
            unset($this->roles[$key]);
        }
    }
}
