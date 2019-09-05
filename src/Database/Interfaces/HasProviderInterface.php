<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface HasProviderInterface
{
    /**
     * Get linked provider to the entity.
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider|null
     */
    public function getProvider(): ?Provider;

    /**
     * Get provider id from provider entity
     *
     * @return int|null
     */
    public function getProviderId(): ?int;

    /**
     * Set provider for the entity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return mixed Returns self for fluency
     */
    public function setProvider(Provider $provider);
}
