<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Task implements ShareableInterface
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
     * @ORM\Column(type="datetime")
     *
     * @Groups({"public"})
     */
    private $createdAt;

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
     * @ORM\ManyToOne(targetEntity="Customer")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Groups({"public"})
     */
    private $customer;

    /**
     * @ORM\OneToMany(
     *   targetEntity=TaskTeam::class,
     *   mappedBy="task",
     *   orphanRemoval=true,
     *   fetch="EAGER"
     * )
     * @ORM\OrderBy({"createdAt" = "DESC"})
     *
     * @Groups({"public"})
     */
    private $teams;

    public function __construct(Company $company, string $name)
    {
        $this->setCompany($company);
        $this->setName($name);
        $this->teams = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
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

    public function setCustomer(?Customer $customer = null): void
    {
        $this->customer = $customer;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function addTeam(TaskTeam $team): void
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
        }
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function getTeam(Team $team): ?TaskTeam
    {
        foreach ($this->getTeams() as $taskTeam) {
            if ($taskTeam->getTeam() === $team) {
                return $taskTeam;
            }
        }

        return null;
    }

    public function removeTeam(TaskTeam $team): void
    {
        if ($this->teams->removeElement($team)) {
            if ($team->getTask() === $this) {
                $team->setTask(null);
            }
        }
    }
}
