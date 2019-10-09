<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Handlers;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivityStub;

/**
 * @coversNothing
 */
final class ActivityHandlerStub implements ActivityHandlerInterface
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface|null
     */
    private $next;

    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface[]
     */
    private $saved = [];

    /**
     * {@inheritdoc}
     */
    public function create(): ProviderAwareActivityInterface
    {
        return new ProviderAwareActivityStub();
    }

    /**
     * {@inheritdoc}
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface
    {
        return $this->next;
    }

    /**
     * Returns saved activities.
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface[]
     */
    public function getSaved(): array
    {
        return $this->saved;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Provider $provider, ProviderAwareActivityInterface $activity): void
    {
        $this->saved[] = $activity;
    }

    /**
     * Set next.
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface $activity
     *
     * @return void
     */
    public function setNext(ProviderAwareActivityInterface $activity): void
    {
        $this->next = $activity;
    }
}
