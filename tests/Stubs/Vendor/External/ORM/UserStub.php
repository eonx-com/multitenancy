<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;

/**
 * @coversNothing
 */
class UserStub implements EntityInterface, HasProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider(): ?Provider
    {
        return new Provider('PROVIDER_ID', 'Loyalty Corp.');
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderId(): ?int
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setProvider(Provider $provider)
    {
        return $this;
    }
}
