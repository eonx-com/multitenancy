<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Activities;

use EoneoPay\Utils\DateTime;
use EoneoPay\Webhooks\Activities\Interfaces\ActivityDataInterface;
use EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface;
use EoneoPay\Webhooks\Payloads\Interfaces\PayloadManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\Interfaces\ProviderAwareActivityFactoryInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface;

final class ProviderAwareActivityFactory implements ProviderAwareActivityFactoryInterface
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface
     */
    private $activityPersister;

    /**
     * @var \EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \EoneoPay\Webhooks\Payloads\Interfaces\PayloadManagerInterface
     */
    private $payloadManager;

    /**
     * Constructor.
     *
     * @param \EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface $eventDispatcher
     * @param \EoneoPay\Webhooks\Payloads\Interfaces\PayloadManagerInterface $payloadManager
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface $persister
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        PayloadManagerInterface $payloadManager,
        ActivityPersisterInterface $persister
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->payloadManager = $payloadManager;
        $this->activityPersister = $persister;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function send(Provider $provider, ActivityDataInterface $activityData, ?\DateTime $now = null): void
    {
        $payload = $this->payloadManager->buildPayload($activityData);

        $activityId = $this->activityPersister->save(
            $provider,
            $activityData::getActivityKey(),
            $activityData->getPrimaryEntity(),
            $now ?? new DateTime('now'),
            $payload
        );

        // Add sequence to newly persisted activity's payload.
        $this->activityPersister->addSequenceToPayload($provider, $activityId);

        $this->eventDispatcher->dispatchActivityCreated($activityId);
    }
}
