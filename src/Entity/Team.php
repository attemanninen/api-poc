<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @ORM\Table(name="team")
 */
class Team
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
     * @ORM\ManyToOne(targetEntity=Company::class)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"public"})
     */
    private $name;

    public function __construct(Company $company, string $name)
    {
        $this->setCompany($company);
        $this->setName($name);
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
}
