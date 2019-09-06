<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\ProviderResolver;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface;

final class ProviderResolverStub implements ProviderResolverInterface
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Database\Entities\Provider|null
     */
    private $provider;

    /**
     * ProviderResolverStub constructor.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider|null $provider
     */
    public function __construct(?Provider $provider = null)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $providerId): Provider
    {
        return $this->provider ?? new Provider('PROVIDER_ID', 'Loyalty Corp');
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(object $entity): Provider
    {
        return $this->provider ?? new Provider('PROVIDER_ID', 'Loyalty Corp');
    }
}
