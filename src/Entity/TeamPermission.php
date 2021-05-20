<?php

namespace App\Entity;

use App\Form\DataTransferObject\TeamPermissionData;
use App\Repository\TeamPermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TeamPermissionRepository::class)
 */
class TeamPermission
{
    /**
     * Team permissions.
     */
    public const TEAM_EDIT = 'team_edit';
    public const TEAM_REMOVE = 'team_remove';

    /**
     * Task permissions.
     */
    public const TASK_VIEW = 'task_view';
    public const TASK_CREATE = 'task_create';
    public const TASK_EDIT = 'task_edit';
    public const TASK_REMOVE = 'task_remove';

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
     *  targetEntity=Team::class
     * )
     * @ORM\JoinColumn(
     *  name="team_id",
     *  referencedColumnName="id",
     *  nullable=false
     * )
     *
     * @Groups({"public"})
     */
    private $team;

    /**
     * @ORM\ManyToOne(
     *   targetEntity=User::class,
     *   inversedBy="teamPermissions"
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"public"})
     */
    private $user;

    /**
     * @ORM\Column(type="array")
     *
     * @Groups({"public"})
     */
    private $permissions;

    public function __construct(Team $team, User $user, array $permissions)
    {
        $this->setTeam($team);
        $this->setUser($user);
        $this->setPermissions($permissions);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = array_values($permissions);
    }

    public function addRole(string $permission): void
    {
        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
        }
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function removeRole(string $permission): void
    {
        $key = array_search($permission, $this->permissions);
        if ($key !== false) {
            unset($this->permissions[$key]);
        }
    }

    public function updateFromDataTransferObject(TeamPermissionData $data): void
    {
        $this->setUser($data->user);
        $this->setPermissions($data->permissions);
    }
}
