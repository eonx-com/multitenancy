<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Entities;

use LoyaltyCorp\Mulitenancy\Database\Entities\Tenant;
use Tests\LoyaltyCorp\Multitenancy\TestCase;

class TenantTest extends TestCase
{
    public function testState(): void
    {
        $entity = new Tenant('abc1235zxc', 'Acme Example Corp');

        self::assertSame('abc1235zxc', $entity->getExternalId());
        self::assertSame('Acme Example Corp', $entity->getName());
    }

    public function testNameSet(): void
    {
        $entity = new Tenant('0x0x0x123', 'Foo');

        $entity->setName('Bob');

        self::assertSame('Bob', $entity->getName());
    }
}
