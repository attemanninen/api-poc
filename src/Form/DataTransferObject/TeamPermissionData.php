<?php

namespace App\Form\DataTransferObject;

use App\Entity\TeamPermission;
use App\Entity\User;

class TeamPermissionData
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $permissions;

    public function __construct()
    {
        $this->permissions = [];
    }

    public static function fromTeamPermission(TeamPermission $teamPermission): self
    {
        $data = new self();
        $data->user = $teamPermission->getUser();
        $data->permissions = $teamPermission->getPermissions();

        return $data;
    }

    public function updateTeamPermission(TeamPermission $teamPermission): void
    {
        $teamPermission->setUser($this->user);
        $teamPermission->setPermissions($this->permissions);
    }
}
