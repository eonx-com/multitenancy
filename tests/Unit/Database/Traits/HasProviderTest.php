<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Traits;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;
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
     * @throws \ReflectionException
     */
    public function testTraitGetterSetter(): void
    {
        $provider = $this->getProvider();
        $dummy = new class
        {
            use HasProvider;
        };
        $dummy->setProvider($provider);

        self::assertSame($provider, $dummy->getProvider());
    }

    /**
     * Tests that when a provider is set, the `providerId` property in the class using the trait has its value updated
     * to match that of the provider id.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testTraitProviderIdSet(): void
    {
        $provider = $this->getProvider();
        $dummy = new class
        {
            use HasProvider;
        };
        $dummy->setProvider($provider);
        $property = (new \ReflectionClass($dummy))->getProperty('providerId');
        $property->setAccessible(true);

        self::assertSame(123, $property->getValue($dummy));
    }

    /**
     * Creates a new provider entity instance and sets its id through reflection.
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     *
     * @throws \ReflectionException
     */
    private function getProvider(): Provider
    {
        $provider = new Provider('test-provider', 'Test Provider');

        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('providerId');
        $property->setAccessible(true);
        $property->setValue($provider, 123);

        return $provider;
    }
}
