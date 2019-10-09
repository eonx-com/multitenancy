<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister;

use DateTime;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\ActivityNotFoundException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface;

final class ActivityPersister implements ActivityPersisterInterface
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface
     */
    private $activityHandler;

    /**
     * Constructor.
     *
     * phpcs:disable
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface $activityHandler
     *
     * phpcs:enable
     */
    public function __construct(ActivityHandlerInterface $activityHandler)
    {
        $this->activityHandler = $activityHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function addSequenceToPayload(Provider $provider, int $activityId): void
    {
        $activity = $this->get($provider, $activityId);

        if (($activity instanceof ProviderAwareActivityInterface) !== true) {
            throw new ActivityNotFoundException(
                \sprintf('No activity "%s" found to add sequence to payload.', $activityId)
            );
        }

        $payload = $activity->getPayload();
        $activity->setPayload(\array_merge($payload, [
            '_sequence' => $activityId,
        ]));

        $this->activityHandler->save($provider, $activity);
    }

    /**
     * {@inheritdoc}
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface
    {
        return $this->activityHandler->get($provider, $activityId);
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        Provider $provider,
        string $activityKey,
        EntityInterface $primaryEntity,
        DateTime $occurredAt,
        array $payload
    ): int {
        $activity = $this->activityHandler->create();
        $activity->setActivityKey($activityKey);
        $activity->setOccurredAt($occurredAt);
        $activity->setPayload($payload);
        $activity->setPrimaryEntity($primaryEntity);
        $activity->setProvider($provider);

        $this->activityHandler->save($provider, $activity);

        return $activity->getActivityId();
    }
}
