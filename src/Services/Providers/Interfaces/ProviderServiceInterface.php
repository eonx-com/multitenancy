<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Providers\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface ProviderServiceInterface
{
    /**
     * Creates a new provider.
     *
     * @param string $providerId
     * @param string $name
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function create(string $providerId, string $name): Provider;
}
