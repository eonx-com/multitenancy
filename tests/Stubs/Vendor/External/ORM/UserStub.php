<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;

/**
 * @coversNothing
 */
class UserStub implements EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function __call(string $method, array $parameters)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fill(array $data): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFillableProperties(): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(): string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(?string $rootNode = null): ?string
    {
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
    }
}
