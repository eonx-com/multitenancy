<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM\Query\FilterCollectionStub;

final class EntityManagerStub implements EntityManagerInterface
{
    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata[]|null
     */
    private $metadatas;

    /**
     * @var \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface[]|null
     */
    private $repositories;

    /**
     * Create entity manager stub.
     *
     * @param mixed $entity
     * @param \Doctrine\ORM\Mapping\ClassMetadata[]|null $metadatas
     * @param \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface[]|null $repositories
     */
    public function __construct(
        $entity = null,
        ?array $metadatas = null,
        ?array $repositories = null
    ) {
        $this->entity = $entity;
        $this->metadatas = $metadatas;
        $this->repositories = $repositories;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Provider $provider, string $className, $entityId): ?object
    {
        return $this->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(Provider $provider, $entity, array $ids): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function flush(Provider $provider): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata(string $className): ClassMetadata
    {
        return $this->metadatas[$className] ?? new ClassMetadata($className);
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
    public function getRepository(string $class)
    {
        return $this->repositories[$class] ?? new MultitenancyRepositoryStub();
    }

    /**
     * {@inheritdoc}
     */
    public function merge(Provider $provider, HasProviderInterface $entity): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Provider $provider, HasProviderInterface $entity): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Provider $provider, HasProviderInterface $entity): void
    {
    }
}
