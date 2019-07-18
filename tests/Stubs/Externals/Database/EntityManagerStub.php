<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\Database;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;

/**
 * @coversNothing
 */
class EntityManagerStub implements EntityManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByIds(string $class, array $ids): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): FilterCollectionInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function merge(EntityInterface $entity): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function persist(EntityInterface $entity): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function remove(EntityInterface $entity): void
    {
    }
}
