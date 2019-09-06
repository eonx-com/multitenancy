<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Listeners\GenerateUniqueValueInterface;

/**
 * This stub entity is to test unique value generation
 *
 * @ORM\Entity(repositoryClass="\Tests\LoyaltyCorp\Multitenancy\Stubs\Database\RepositoryStub")
 */
final class EntityWithGenerateUniqueValueInterfaceStub implements GenerateUniqueValueInterface, HasProviderInterface
{
    use HasProvider;

    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     *
     * @var string Internal Database ID.
     */
    private $entityId;

    /**
     * Generated property
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    private $generatedValue;

    /**
     * Whether this entity has a check digit or not
     *
     * @var bool
     */
    private $withCheckDigit;

    /**
     * Create entity stub
     *
     * @param bool|null $withCheckDigit
     */
    public function __construct(?bool $withCheckDigit = null)
    {
        $this->withCheckDigit = $withCheckDigit ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function areGeneratorsEnabled(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function disableGenerators()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function enableGenerators()
    {
    }

    /**
     * Get entity id
     *
     * @return string|null
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedProperty(): string
    {
        return 'generatedValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneratedPropertyLength(): int
    {
        return 9;
    }

    /**
     * Get generated value
     *
     * @return string|null
     */
    public function getGeneratedValue(): ?string
    {
        return $this->generatedValue;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGeneratedPropertyCheckDigit(): bool
    {
        return $this->withCheckDigit;
    }

    /**
     * Set generated value
     *
     * @param string $value The value to set
     *
     * @return mixed
     */
    public function setGeneratedValue(string $value)
    {
        $this->generatedValue = $value;

        return $this;
    }
}
