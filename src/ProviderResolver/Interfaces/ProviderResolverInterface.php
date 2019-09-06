<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface ProviderResolverInterface
{
    /**
     * Find provider by id.
     *
     * @param int $providerId The id of the provider to find
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function find(int $providerId): Provider;

    /**
     * Resolve provider from entity.
     *
     * @param object $entity
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function resolve(object $entity): Provider;
}
