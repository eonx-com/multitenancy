<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

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
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function resolve(EntityInterface $entity): Provider;
}
