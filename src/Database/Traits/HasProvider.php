<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Traits;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

/**
 * @ORM\MappedSuperclass
 */
trait HasProvider
{
    /**
     * Relationship to providerId in Provider
     *
     * @ORM\ManyToOne(targetEntity="LoyaltyCorp\Multitenancy\Database\Entities\Provider")
     * @ORM\JoinColumn(name="providerId", referencedColumnName="id", nullable=true)
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
        $this->providerId = $this->provider !== null ? $this->provider->getProviderId() : null;

        return $this->provider;
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
        $this->providerId = $provider->getProviderId();
    }
}
