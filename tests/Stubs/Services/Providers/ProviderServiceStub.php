<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Providers;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;

final class ProviderServiceStub implements ProviderServiceInterface
{
    /**
     * Creates a new provider.
     *
     * @param string $providerId
     * @param string $name
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    public function createOrFind(string $providerId, string $name): Provider
    {
        return new Provider('test-provider', 'Test Provider');
    }
}
