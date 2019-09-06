<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;

interface ProviderResolverInterface
{
    /**
     * Find provider by id
     *
     * @param int $providerId The id of the provider to find
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function find(int $providerId): Provider;

    /**
     * Resolve provider from entity
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface $entity
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function resolve(HasProviderInterface $entity): Provider;
}
