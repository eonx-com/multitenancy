<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Entities;

use LoyaltyCorp\Mulitenancy\Database\Entities\Provider;
use Tests\LoyaltyCorp\Multitenancy\BaseTestCase;

/**
 * @covers \LoyaltyCorp\Mulitenancy\Database\Entities\Provider
 */
class ProviderTest extends BaseTestCase
{
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
    }
}
