<?php

namespace App\Form\DataTransferObject;

use App\Entity\GroupRole;
use App\Entity\User;

class GroupRoleData
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var array
     */
    public $roles;

    public function __construct()
    {
        $this->roles = [];
    }

    public static function fromGroupRole(GroupRole $groupRole): self
    {
        $data = new self();
        $data->user = $groupRole->getUser();
        $data->roles = $groupRole->getRoles();

        return $data;
    }

    public function updateGroupRole(GroupRole $groupRole): void
    {
        $groupRole->setUser($this->user);
        $groupRole->setRoles($this->roles);
    }
}
