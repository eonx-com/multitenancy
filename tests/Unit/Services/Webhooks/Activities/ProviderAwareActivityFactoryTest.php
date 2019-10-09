<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Activities;

use EoneoPay\Utils\DateTime;
use EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface;
use EoneoPay\Webhooks\Payloads\Interfaces\PayloadManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\ProviderAwareActivityFactory;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Persisters\ActivityPersisterStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Webhooks\Activity\ActivityDataStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Webhooks\Event\EventDispatcherStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Webhooks\Payload\PayloadManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\ProviderAwareActivityFactory
 */
final class ProviderAwareActivityFactoryTest extends AppTestCase
{
    /**
     * Returns the instance under test.
     *
     * @param \EoneoPay\Webhooks\Events\Interfaces\EventDispatcherInterface $dispatcher
     * @param \EoneoPay\Webhooks\Payloads\Interfaces\PayloadManagerInterface $payloadManager
     * @param \LoyaltyCorp\Multitenancy\Services\Webhooks\Persisters\Interfaces\ActivityPersisterInterface $persister
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Activities\ProviderAwareActivityFactory
     */
    public function getManager(
        EventDispatcherInterface $dispatcher,
        PayloadManagerInterface $payloadManager,
        ActivityPersisterInterface $persister
    ): ProviderAwareActivityFactory {
        return new ProviderAwareActivityFactory(
            $dispatcher,
            $payloadManager,
            $persister
        );
    }

    /**
     * Test send method.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testSend(): void
    {
        $occurredAt = new DateTime('2011-01-01T00:00:00');

        $activityData = new ActivityDataStub();

        $expectedEvent = [5];
        $expectedActivity = [
            [
                'activityKey' => 'activity.constant',
                'entity' => $activityData->getPrimaryEntity(),
                'occurredAt' => $occurredAt,
                'payload' => [
                    'payload' => 'wot',
                ],
            ],
        ];

        $activityPersister = new ActivityPersisterStub();
        $activityPersister->setNextSequence(5);
        $dispatcher = new EventDispatcherStub();
        $payloadManager = new PayloadManagerStub();
        $payloadManager->addPayload(['payload' => 'wot']);

        $manager = $this->getManager(
            $dispatcher,
            $payloadManager,
            $activityPersister
        );

        $manager->send(
            new Provider('provider', 'Test Provider Inc.'),
            $activityData,
            $occurredAt
        );

        self::assertSame($expectedActivity, $activityPersister->getSaved());
        self::assertSame($expectedEvent, $dispatcher->getActivityCreated());
        self::assertSame(5, $activityPersister->getAddedSequence());
    }

    /**
     * Test send method with default time.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     */
    public function testSendDefaultTime(): void
    {
        // Testing default value of a $now variable. Asserted below with generous
        // delta.
        $expectedDate = new DateTime('now');

        $activityData = new ActivityDataStub();

        $activityPersister = new ActivityPersisterStub();
        $activityPersister->setNextSequence(5);
        $dispatcher = new EventDispatcherStub();
        $payloadManager = new PayloadManagerStub();
        $payloadManager->addPayload(['payload' => 'wot']);

        $manager = $this->getManager(
            $dispatcher,
            $payloadManager,
            $activityPersister
        );

        $manager->send(new Provider('provider', 'Test Provider Inc.'), $activityData);

        $saved = $activityPersister->getSaved();
        $activity = \reset($saved);

        self::assertArrayHasKey('occurredAt', $activity);

        // Asserts the expected date is within 10 seconds of the generated now inside the
        // service.
        self::assertEqualsWithDelta($expectedDate, $activity['occurredAt'], 10);
    }
}
