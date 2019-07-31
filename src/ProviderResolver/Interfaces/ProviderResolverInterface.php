<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface ProviderResolverInterface
{
    /**
     * Get resolved provider.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $entity
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function getProvider(EntityInterface $entity): Provider;
}
