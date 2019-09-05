<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;

interface EntityManagerInterface
{
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
     * Flush unit of work to the database, ensuring all entities belong to the correct provider if applicable
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own all entities in UOW
     *
     * @return void
     */
    public function flush(Provider $provider): void;

    /**
     * Gets the filters attached to the entity manager.
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface
     */
    public function getFilters(): FilterCollectionInterface;

    /**
     * Gets the repository from a entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return mixed The instantiated repository
     */
    public function getRepository(string $class);

    /**
     * Merge entity to the database, similar to REPLACE INTO in SQL
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own this entity
     * @param \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $entity The entity to merge
     *
     * @return void
     */
    public function merge(Provider $provider, HasProviderInterface $entity): void;

    /**
     * Persist entity to the database
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
