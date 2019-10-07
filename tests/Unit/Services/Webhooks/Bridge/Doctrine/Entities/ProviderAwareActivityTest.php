<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Bridge\Doctrine\Entities;

use EoneoPay\Utils\DateTime;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivity;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivity
 */
final class ProviderAwareActivityTest extends AppTestCase
{
    /**
     * Tests the misc methods.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException
     * @throws \ReflectionException
     */
    public function testMethods(): void
    {
        $activity = $this->getActivityEntity();

        self::assertSame(123, $activity->getId());
        self::assertSame(123, $activity->getActivityId());
        self::assertSame('activity.key', $activity->getActivityKey());
        self::assertSame(['payload'], $activity->getPayload());
        self::assertSame(EntityStub::class, $activity->getPrimaryClass());
        self::assertSame('55', $activity->getPrimaryId());
        self::assertEquals(new DateTime('2100-01-01T10:11:12Z'), $activity->getOccurredAt());
    }

    /**
     * Tests the toArray method.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException
     * @throws \ReflectionException
     */
    public function testToArray(): void
    {
        $expected = [
            'activity_key' => 'activity.key',
            'id' => 123,
            'occurred_at' => '2100-01-01T10:11:12Z',
            'payload' => [
                'payload',
            ],
        ];

        $activity = $this->getActivityEntity();

        self::assertSame($expected, $activity->toArray());
    }

    /**
     * Create activity entity.
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivity
     *
     * @throws \EoneoPay\Utils\Exceptions\InvalidDateTimeStringException
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException
     * @throws \ReflectionException
     */
    private function getActivityEntity(): ProviderAwareActivity
    {
        $activityReflection = new ReflectionClass(ProviderAwareActivity::class);

        /**
         * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivity $activity
         */
        $activity = $activityReflection->newInstanceWithoutConstructor();
        $activity->setActivityKey('activity.key');
        $activity->setActivityId(123);
        $activity->setOccurredAt(new DateTime('2100-01-01T10:11:12Z'));
        $activity->setPayload(['payload']);
        $activity->setPrimaryEntity(new EntityStub());
        $activity->setProvider(new Provider('provider', 'Provider Inc.'));

        return $activity;
    }
}
