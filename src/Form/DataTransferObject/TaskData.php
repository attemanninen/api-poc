<?php

namespace App\Form\DataTransferObject;

use App\Entity\Customer;
use App\Entity\Task;

class TaskData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    public ?Customer $customer;

    /**
     * @var array
     */
    public $teams;

    public function __construct()
    {
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
