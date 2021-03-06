<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Repositories;

use LoyaltyCorp\Multitenancy\Externals\ORM\Repository;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Interfaces\Repositories\ProviderAwareActivityRepositoryInterface; // phpcs:ignore

final class ProviderAwareActivityRepository extends Repository implements ProviderAwareActivityRepositoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore This method is exceptionally difficult to test given the incomplete
     * doctrine setup in this libraries test suite and the fact the query builder and query
     * inside doctrine cannot be stubbed (or replaced with mocks due to the final Query class).
     */
    public function getFillIterable(): iterable
    {
        $builder = $this->createQueryBuilder('w');

        foreach ($builder->getQuery()->iterate() as $result) {
            yield $result[0];
        }
    }
}
