<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\Interfaces;

use EoneoPay\Webhooks\Activities\Interfaces\ActivityDataInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

/**
 * The ActivityFactory is the entrypoint for where activities are lodged with the
 * system by project code.
 *
 * This factory will create and save an ActivityInterface entity and dispatch an event
 * that allows worker queues to asynchronously handle webhook sending.
 *
 * This service is not responsible for actual webhook processing, see README.md for
 * more detail about how the process proceeds.
 */
interface ProviderAwareActivityFactoryInterface
{
    /**
     * The entry point for creating activities.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param \EoneoPay\Webhooks\Activities\Interfaces\ActivityDataInterface $activityData
     *
     * @return void
     */
    public function send(Provider $provider, ActivityDataInterface $activityData): void;
}
