<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\ProviderResolver;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\ProviderResolver\Interfaces\ProviderResolverInterface;

/**
 * @coversNothing
 */
class ProviderResolverStub implements ProviderResolverInterface
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
    public function getProvider(EntityInterface $entity): Provider
    {
        return $this->provider ?? new Provider('PROVIDER_ID', 'Loyalty Corp');
    }
}