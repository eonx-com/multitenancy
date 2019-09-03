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
     * Set provider for the entity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return mixed Returns self for fluency
     */
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;
        $this->providerId = $provider->getProviderId();

        return $this;
    }
}
