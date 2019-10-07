<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;

interface EntityManagerInterface
{
    /**
     * Finds an object by its identifier.
     *
     * This is just a convenient shortcut for getRepository($className)->find($id).
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own all entities in UOW
     * @param string $className The class name of the object to find.
     * @param mixed $entityId The identity of the object to find.
     *
     * @return object|null The found object.
     */
    public function find(Provider $provider, string $className, $entityId): ?object;

    /**
     * Finds an entity by its identifier.
     *
     * Does NOT currently support composite identifiers.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own this entity
     * @param object|string $entity An entity object or string containing a class name
     * @param mixed[] $ids Multiple ids to find entities by
     *
     * @return object[]
     */
    public function findByIds(Provider $provider, $entity, array $ids): array;

    /**
     * Flush unit of work to the database, ensuring all entities belong to the correct provider if applicable.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own all entities in UOW
     *
     * @return void
     */
    public function flush(Provider $provider): void;

    /**
     * Returns the ClassMetadata descriptor for a class.
     *
     * The class name must be the fully-qualified class name without a leading backslash
     * (as it is returned by get_class($obj)).
     *
     * @param string $className
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata(string $className): ClassMetadata;

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface;

    /**
     * Gets the repository from a entity class.
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return mixed
     */
    public function getRepository(string $class);

    /**
     * Merge entity to the database, similar to REPLACE INTO in SQL.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own this entity
     * @param \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $entity The entity to merge
     *
     * @return void
     */
    public function merge(Provider $provider, HasProviderInterface $entity): void;

    /**
     * Persist entity to the database.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own this entity
     * @param \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $entity The entity to persist
     *
     * @return void
     */
    public function persist(Provider $provider, HasProviderInterface $entity): void;

    /**
     * Remove entity from the database.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own this entity
     * @param \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $entity The entity to remove
     *
     * @return void
     */
    public function remove(Provider $provider, HasProviderInterface $entity): void;
}
