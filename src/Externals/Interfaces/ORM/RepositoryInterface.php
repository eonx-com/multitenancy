<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface RepositoryInterface
{
    /**
     * Counts entities by a set of criteria.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider to limit search criteria to
     * @param mixed[]|null $criteria The search criteria
     *
     * @return int The cardinality of the objects matching search criteria
     */
    public function count(Provider $provider, ?array $criteria = null): int;

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param mixed $entityId The entity identifier
     *
     * @return object|null The entity matching the identifier or null if no matching entity found
     */
    public function find(Provider $provider, $entityId): ?object;

    /**
     * Finds all objects in the repository.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider to limit search criteria to
     *
     * @return object[] All matching entities
     */
    public function findAll(Provider $provider): array;

    /**
     * Finds objects by a set of criteria.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider to limit search criteria to
     * @param mixed[] $criteria The search criteria
     * @param string[]|null $orderBy How to order results
     * @param int|null $limit The number of results to retrieve
     * @param int|null $offset Number of records to skip when searching
     *
     * @return object[] Entities matching search criteria
     */
    public function findBy(
        Provider $provider,
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array;

    /**
     * Finds a single object by a set of criteria.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider to limit search criteria to
     * @param mixed[] $criteria The search criteria
     * @param string[]|null $orderBy How to order results
     *
     * @return object|null First entity matching search criteria or null if no matches found
     */
    public function findOneBy(Provider $provider, array $criteria, ?array $orderBy = null): ?object;

    /**
     * Returns the class name of the object managed by the repository.
     *
     * @return string
     */
    public function getClassName(): string;
}
