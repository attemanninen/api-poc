<?php

namespace App\Form\DataTransferObject;

use App\Entity\Group;

class GroupData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $groupRoles;

    public function __construct()
    {
        $this->groupRoles = [];
    }

    public static function fromGroup(Group $group, array $groupRoles = []): self
    {
        $data = new self();
        $data->name = $group->getName();

        foreach ($groupRoles as $groupRole) {
            $data->groupRoles[] = GroupRoleData::fromGroupRole($groupRole);
        }

        return $data;
    }

    public function updateGroup(Group $group): void
    {
        $group->setName($this->name);
    }
}
