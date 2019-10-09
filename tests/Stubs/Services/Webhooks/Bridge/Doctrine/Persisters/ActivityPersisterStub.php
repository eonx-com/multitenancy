<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Persisters;

use DateTime;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface;

/**
 * @coversNothing
 */
final class ActivityPersisterStub implements ActivityPersisterInterface
{
    /**
     * Sequence added to the activity payload.
     *
     * @var int|null
     */
    private $addedSequence;

    /**
     * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface|null
     */
    private $nextActivity;

    /**
     * @var int
     */
    private $nextSequence = 1;

    /**
     * @var mixed[]
     */
    private $saved = [];

    /**
     * {@inheritdoc}
     */
    public function addSequenceToPayload(Provider $provider, int $activityId): void
    {
        $this->addedSequence = $activityId;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface
    {
        return $this->nextActivity;
    }

    /**
     * Get added sequence the payload.
     *
     * @return int|null
     */
    public function getAddedSequence(): ?int
    {
        return $this->addedSequence;
    }

    /**
     * @return mixed[]
     */
    public function getSaved(): array
    {
        return $this->saved;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        Provider $provider,
        string $activityKey,
        EntityInterface $entity,
        DateTime $occurredAt,
        array $payload
    ): int {
        $this->saved[] = \compact('activityKey', 'entity', 'occurredAt', 'payload');

        return $this->nextSequence;
    }

    /**
     * Sets next activity returned by get.
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface $activity
     *
     * @return void
     */
    public function setNextActivity(ProviderAwareActivityInterface $activity): void
    {
        $this->nextActivity = $activity;
    }

    /**
     * Sets the next sequence.
     *
     * @param int $seq
     *
     * @return void
     */
    public function setNextSequence(int $seq): void
    {
        $this->nextSequence = $seq;
    }
}
