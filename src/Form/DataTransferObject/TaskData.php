<?php

namespace App\Form\DataTransferObject;

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

    /**
     * @var array
     */
    public $groups;

    public function __construct()
    {
        $this->groups = [];
    }

    public static function fromTask(Task $task): self
    {
        $data = new self();
        $data->name = $task->getName();
        $data->description = $task->getDescription();

        foreach ($task->getGroups() as $taskGroup) {
            $data->groups[] = $taskGroup->getGroup();
        }

        return $data;
    }

    public function updateTask(Task $task): void
    {
        $task->setName($this->name);
        $task->setDescription($this->description);
    }
}
