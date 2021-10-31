<?php

namespace App\Entity;

use App\Repository\SharingRuleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SharingRuleRepository::class)
 */
class SharingRule
{
    public const EFFECT_ALLOW = 'allow';
    public const EFFECT_DENY = 'deny';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subjectType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subjectId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modelClass;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modelProperty;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $modelValue;

    /**
     * @ORM\Column(type="array")
     */
    private $permissions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $effect;

    public function __construct(
        Company $company,
        string $subjectType,
        $subjectId,
        string $modelClass,
        string $modelProperty,
        $modelValue,
        array $permissions,
        string $effect
    ) {
        $this->setCompany($company);
        $this->setSubjectType($subjectType);
        $this->setSubjectId($subjectId);
        $this->setModelClass($modelClass);
        $this->setModelProperty($modelProperty);
        $this->setModelValue($modelValue);
        $this->setPermissions($permissions);
        $this->setEffect($effect);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setSubjectType(string $subjectType): void
    {
        $this->subjectType = $subjectType;
    }

    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    public function setSubjectId($subjectId): void
    {
        $this->subjectId = (string) $subjectId;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function setModelClass(string $modelClass): void
    {
        $this->modelClass = $modelClass;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function setModelProperty(string $modelProperty): void
    {
        $this->modelProperty = $modelProperty;
    }

    public function getModelProperty(): string
    {
        return $this->modelProperty;
    }

    public function setModelValue($modelValue): void
    {
        $this->modelValue = (string) $modelValue;
    }

    public function getModelValue(): string
    {
        return $this->modelValue;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setEffect(string $effect): void
    {
        $this->effect = $effect;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }
}
