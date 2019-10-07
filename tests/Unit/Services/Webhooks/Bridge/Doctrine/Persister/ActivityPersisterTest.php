<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Bridge\Doctrine\Persister;

use EoneoPay\Utils\DateTime;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\ActivityNotFoundException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister\ActivityPersister;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivityStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandlerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister\ActivityPersister
 */
final class ActivityPersisterTest extends AppTestCase
{
    /**
     * Test that adding sequence to activity payload returns payload with
     * activity sequence.
     *
     * @return void
     */
    public function testAddSequenceToPayload(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $activity = new ProviderAwareActivityStub();
        $activity->setPayload(['key' => 'value']);
        $activityHandler = new ActivityHandlerStub();
        $activityHandler->setNext($activity);
        $expectedPayload = [
            'key' => 'value',
            '_sequence' => 5,
        ];
        $persister = $this->getPersister($activityHandler);

        $persister->addSequenceToPayload($provider, 5);

        self::assertSame($expectedPayload, $activity->getPayload());
    }

    /**
     * Test that adding sequence to activity payload throws exception when invalid or
     * unknown activity id is provided.
     *
     * @return void
     */
    public function testAddSequenceToPayloadThrowsNotFoundException(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $activityHandler = new ActivityHandlerStub();
        $persister = $this->getPersister($activityHandler);

        $this->expectException(ActivityNotFoundException::class);
        $this->expectExceptionMessage('No activity "111" found to add sequence to payload.');

        $persister->addSequenceToPayload($provider, 111);
    }

    /**
     * Tests get.
     *
     * @return void
     */
    public function testGet(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $activity = new ProviderAwareActivityStub();
        $activityHandler = new ActivityHandlerStub();
        $activityHandler->setNext($activity);

        $persister = $this->getPersister($activityHandler);

        $result = $persister->get($provider, 5);

        self::assertSame($activity, $result);
    }

    /**
     * Tests Save.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testSave(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $occurredAt = new DateTime();

        $expectedSaved = [
            'constant' => 'activity.constant',
            'occurredAt' => $occurredAt,
            'payload' => ['payload'],
        ];

        $activityHandler = new ActivityHandlerStub();
        $persister = $this->getPersister($activityHandler);

        $activityId = $persister->save(
            $provider,
            'activity.constant',
            new EntityStub(),
            $occurredAt,
            ['payload']
        );

        self::assertSame(1, $activityId);

        $saved = $activityHandler->getSaved()[0];
        self::assertInstanceOf(ProviderAwareActivityStub::class, $saved);
        self::assertSame($expectedSaved, $saved->getData());
    }

    /**
     * Get instance under test.
     *
     * phpcs:disable
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface|null $activityHandler
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Persister\ActivityPersister
     *
     * phpcs:enable
     */
    private function getPersister(
        ?ActivityHandlerInterface $activityHandler = null
    ): ActivityPersister {
        return new ActivityPersister(
            $activityHandler ?? new ActivityHandlerStub()
        );
    }
}
