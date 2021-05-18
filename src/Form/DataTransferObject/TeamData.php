<?php

namespace App\Form\DataTransferObject;

use App\Entity\Team;

class TeamData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $teamPermissions;

    public function __construct()
    {
        $this->teamPermissions = [];
    }

    public static function fromTeam(Team $team, array $teamPermissions = []): self
    {
        $data = new self();
        $data->name = $team->getName();

        foreach ($teamPermissions as $teamPermission) {
            $data->teamPermissions[] = TeamPermissionData::fromTeamPermission($teamPermission);
        }

        return $data;
    }

    public function updateTeam(Team $team): void
    {
        $team->setName($this->name);
    }
}
