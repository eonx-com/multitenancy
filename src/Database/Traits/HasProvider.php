<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Traits;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException;

/**
 * @ORM\MappedSuperclass
 */
trait HasProvider
{
    /**
     * Relationship to providerId in Provider
     *
     * @ORM\ManyToOne(targetEntity="LoyaltyCorp\Multitenancy\Database\Entities\Provider")
     *
     * @var \LoyaltyCorp\Multitenancy\Database\Entities\Provider|null
     */
    protected $provider;

    /**
     * Get linked provider to the entity.
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     */
    public function getProvider(): Provider
    {
        // Force provider to be set
        if (($this->provider instanceof Provider) === false) {
            throw new ProviderNotSetException();
        }

        return $this->provider;
    }

    /**
     * Get linked provider id.
     *
     * @return int|null
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     */
    public function getProviderId(): ?int
    {
        return $this->getProvider()->getProviderId();
    }

    /**
     * Set provider for the entity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return void
     */
    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }
}
