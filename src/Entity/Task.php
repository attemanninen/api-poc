<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
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
     * @ORM\ManyToOne(targetEntity="Company")
     *
     * @Groups({"public"})
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"public"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({"public"})
     */
    private $description;

    /**
     * @ORM\OneToMany(
     *   targetEntity=TaskGroup::class,
     *   mappedBy="task",
     *   orphanRemoval=true,
     *   fetch="EAGER"
     * )
     * @ORM\OrderBy({"createdAt" = "DESC"})
     *
     * @Groups({"public"})
     */
    private $groups;

    public function __construct(Company $company, string $name)
    {
        $this->setCompany($company);
        $this->setName($name);
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function addGroup(TaskGroup $group): void
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getGroup(Group $group): ?TaskGroup
    {
        foreach ($this->getGroups() as $taskGroup) {
            if ($taskGroup->getGroup() === $group) {
                return $taskGroup;
            }
        }

        return null;
    }

    public function removeGroup(TaskGroup $group): void
    {
        if ($this->groups->removeElement($group)) {
            if ($group->getTask() === $this) {
                $group->setTask(null);
            }
        }
    }
}