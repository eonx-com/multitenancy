<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Persister\Interfaces;

use DateTime;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Model\ProviderAwareActivityInterface;

interface ActivityPersisterInterface
{
    /**
     * Add sequence number to existing activity payload.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param int $activityId Activity id.
     *
     * @return void
     */
    public function addSequenceToPayload(Provider $provider, int $activityId): void;

    /**
     * Returns an activity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param int $activityId
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Model\ProviderAwareActivityInterface|null
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface;

    /**
     * Saves the ActivityData object with the generated payload.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param string $activityKey
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface $primaryEntity
     * @param \DateTime $occurredAt
     * @param mixed[] $payload
     *
     * @return int
     */
    public function save(
        Provider $provider,
        string $activityKey,
        EntityInterface $primaryEntity,
        DateTime $occurredAt,
        array $payload
    ): int;
}
