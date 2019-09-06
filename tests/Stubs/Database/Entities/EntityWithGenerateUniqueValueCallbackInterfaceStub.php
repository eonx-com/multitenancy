<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Listeners\GenerateUniqueValueWithCallbackInterface;

/**
 * This stub entity is to test unique value generation with callback
 *
 * @ORM\Entity(repositoryClass="\Tests\LoyaltyCorp\Multitenancy\Stubs\Database\RepositoryStub")
 */
final class EntityWithGenerateUniqueValueCallbackInterfaceStub implements
    GenerateUniqueValueWithCallbackInterface,
    HasProviderInterface
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
    public function getGeneratedPropertyCallback(string $generatedValue): void
    {
        $this->generatedValue = 'callback';
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
        return false;
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
