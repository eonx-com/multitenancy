<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Traits;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException;

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
     * @var \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    protected $provider;

    /**
     * The id of the related provider entity.
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $providerId;

    /**
     * Get linked provider to the entity.
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider|null
     */
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    /**
     * Get provider id from provider entity
     *
     * @return int|null
     */
    public function getProviderId(): ?int
    {
        return ($this->getProvider() instanceof Provider) === true ? $this->getProvider()->getProviderId() : null;
    }

    /**
     * Set provider for the entity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return mixed Returns self for fluency
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function setProvider(Provider $provider)
    {
        // If provider is already set and it's not the same as what was passed through, deny
        if (($this->getProvider() instanceof Provider) === true && $this->getProvider() !== $provider) {
            throw new ProviderAlreadySetException(
                'A provider has already been set on this entity and can not be changed.'
            );
        }

        $this->provider = $provider;
        $this->providerId = $provider->getProviderId();

        return $this;
    }
}
