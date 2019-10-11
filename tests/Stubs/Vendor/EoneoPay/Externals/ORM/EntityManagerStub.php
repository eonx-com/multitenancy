<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\Query\FilterCollectionStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityRepositoryStub;

final class EntityManagerStub implements EntityManagerInterface
{
    /**
     * Entities.
     *
     * @var object[]|null
     */
    private $entities;

    /**
     * EntityManagerStub constructor.
     *
     * @param object[]|null $entities
     */
    public function __construct(?array $entities = null)
    {
        $this->entities = $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(string $class, array $ids): array
    {
        return [];
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
        return new FilterCollectionStub();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class): RepositoryInterface
    {
        return new EntityRepositoryStub($this->entities);
    }

    /**
     * {@inheritdoc}
     *
     * @return void
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
