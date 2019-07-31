<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Entities;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\EntityTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Entities\Provider
 */
class ProviderTest extends EntityTestCase
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

    /**
     * {@inheritdoc}
     */
    protected function getEntityClass(): string
    {
        return Provider::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityInstance(): EntityInterface
    {
        return new Provider('test-provider', 'Test Provider');
    }

    /**
     * {@inheritdoc}
     */
    protected function getToArrayKeys(): array
    {
        return ['external_id', 'name'];
    }
}
