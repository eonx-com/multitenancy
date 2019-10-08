<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface;

interface ActivityHandlerInterface
{
    /**
     * Creates a new real instance of ActivityInterface.
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface
     */
    public function create(): ProviderAwareActivityInterface;

    /**
     * Returns an activity.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param int $activityId
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface|null
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface;

    /**
     * Saves the webhook.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface $activity
     *
     * @return void
     */
    public function save(Provider $provider, ProviderAwareActivityInterface $activity): void;
}
