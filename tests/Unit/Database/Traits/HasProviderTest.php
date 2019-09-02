<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Traits;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Traits\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Traits\HasProvider
 */
class HasProviderTest extends AppTestCase
{
    /**
     * Tests that the getter returns the same provider instance as what was set through the setter.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     * @throws \ReflectionException
     */
    public function testTraitGetterSetter(): void
    {
        $provider = $this->createProvider();
        $dummy = new EntityHasProviderStub();
        $dummy->setProvider($provider);

        self::assertSame($provider, $dummy->getProvider());
    }

    /**
     * Tests that when a provider is set, the `providerId` property in the class using the trait has its value updated
     * to match that of the provider id.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     * @throws \ReflectionException
     */
    public function testTraitProviderIdSet(): void
    {
        $provider = $this->createProvider();
        $dummy = new EntityHasProviderStub();
        $dummy->setProvider($provider);

        self::assertSame(123, $dummy->getProviderId());
    }

    /**
     * Test trait throws an exception if provider is called but not set
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderNotSetException If provider isn't set prior to call
     */
    public function testTraitThrowsExceptionIfProviderNotSet(): void
    {
        $dummy = new EntityHasProviderStub();

        $this->expectException(ProviderNotSetException::class);

        $dummy->getProvider();
    }

    /**
     * Creates a new provider entity instance and sets its id through reflection.
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     *
     * @throws \ReflectionException
     */
    private function createProvider(): Provider
    {
        $provider = new Provider('test-provider', 'Test Provider');

        $reflection = new ReflectionClass($provider);
        $property = $reflection->getProperty('providerId');
        $property->setAccessible(true);
        $property->setValue($provider, 123);

        return $provider;
    }
}
