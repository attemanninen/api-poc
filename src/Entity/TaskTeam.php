<?php

namespace App\Entity;

use App\Repository\TaskTeamRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskTeamRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class TaskTeam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $task;

    /**
     * @ORM\ManyToOne(
     *   targetEntity=Team::class,
     *   fetch="EAGER"
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"public"})
     */
    private $team;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Groups({"public"})
     */
    private $createdAt;

    public function __construct(Task $task, Team $team)
    {
        $this->task = $task;
        $this->team = $team;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->setCreatedAt(new DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
