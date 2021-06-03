<?php

namespace App\Form\DataTransferObject;

use App\Entity\Customer;
use App\Entity\Task;

class TaskData
{
    public string $name;

    public ?string $description;

    public ?Customer $customer;

    public iterable $teams;

    public function __construct()
    {
        $this->description = null;
        $this->customer = null;
        $this->teams = [];
    }

    public static function fromTask(Task $task): self
    {
        $data = new self();
        $data->name = $task->getName();
        $data->description = $task->getDescription();
        $data->customer = $task->getCustomer();

        foreach ($task->getTeams() as $taskTeam) {
            $data->teams[] = $taskTeam->getTeam();
        }

        return $data;
    }

    public function updateTask(Task $task): void
    {
        $task->setName($this->name);
        $task->setDescription($this->description);
        $task->setCustomer($this->customer);
    }
}
