<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Entities;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use ReflectionProperty;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Entities\Provider
 */
final class ProviderTest extends AppTestCase
{
    /**
     * Tests that the flow config methods return expected data.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testFlowConfigable(): void
    {
        $entity = new Provider('abc1235zxc', 'Acme Example Corp');

        $reflProp = new ReflectionProperty(Provider::class, 'providerId');
        $reflProp->setAccessible(true);
        $reflProp->setValue($entity, 5);

        self::assertSame('multi_tenancy_provider', $entity->getEntityType());
        self::assertSame('5', $entity->getEntityId());
    }

    /**
     * Test getId when entity has an id.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testGetId(): void
    {
        $entity = new Provider('abc1235zxc', 'Acme Example Corp');

        $reflProp = new ReflectionProperty(Provider::class, 'providerId');
        $reflProp->setAccessible(true);
        $reflProp->setValue($entity, 5);

        self::assertSame(5, $entity->getId());
    }

    /**
     * Test getId when entity is new.
     *
     * @return void
     */
    public function testGetIdNull(): void
    {
        $entity = new Provider('abc1235zxc', 'Acme Example Corp');

        self::assertNull($entity->getId());
    }

    /**
     * Test that the name can be overridden.
     *
     * @return void
     */
    public function testNameSet(): void
    {
        $entity = new Provider('0x0x0x123', 'Foo');

        $entity->setName('Bob');

        self::assertSame('Bob', $entity->getName());
    }

    /**
     * Test that getters return text in constructor.
     *
     * @return void
     */
    public function testState(): void
    {
        $entity = new Provider('abc1235zxc', 'Acme Example Corp');

        self::assertSame('abc1235zxc', $entity->getExternalId());
        self::assertSame('Acme Example Corp', $entity->getName());
        self::assertNull($entity->getProviderId());
    }
}
