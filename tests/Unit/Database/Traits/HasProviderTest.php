<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Traits;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Traits\HasProviderBlankStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Traits\HasProvider
 */
final class HasProviderTest extends AppTestCase
{
    /**
     * Test provider does not allow a different provider to be set if one is already set.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     * @throws \ReflectionException
     */
    public function testSetProviderIsImmutable(): void
    {
        $provider1 = $this->createProvider();
        $provider2 = $this->createProvider();

        $entity = new HasProviderBlankStub();
        $entity->setProvider($provider1);

        // Test we can reset the same provider without issue
        $entity->setProvider($provider1);
        $this->addToAssertionCount(1);

        // Test we can't change provider
        $this->expectException(ProviderAlreadySetException::class);
        $entity->setProvider($provider2);
    }

    /**
     * Tests that when a provider is set, the `getProviderId` method returns the id from the associated provider and
     * not what it previously saved on set.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     * @throws \ReflectionException
     */
    public function testTraitGetProviderIdAlwaysReturnsProviderId(): void
    {
        // Create provider and set against an entity
        $provider = $this->createProvider();
        $entity = new HasProviderBlankStub();
        $entity->setProvider($provider);

        // Set up both classes so we can modify private properties
        $reflectedEntity = new ReflectionClass($entity);
        $reflectedProvider = new ReflectionClass($provider);

        // Test provider id was set
        $entityProviderId = $reflectedEntity->getProperty('providerId');
        $entityProviderId->setAccessible(true);
        self::assertSame(123, $entityProviderId->getValue($entity));

        // Test getter returns correct value
        self::assertSame(123, $entity->getProviderId());

        // Change provider id
        $providerProviderId = $reflectedProvider->getProperty('providerId');
        $providerProviderId->setAccessible(true);
        $providerProviderId->setValue($provider, 456);

        // Test provider id hasn't changed on entity
        self::assertSame(123, $entityProviderId->getValue($entity));

        // Test getter returns correct value
        self::assertSame(456, $entity->getProviderId());
    }

    /**
     * Tests that the getter returns the same provider instance as what was set through the setter.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     * @throws \ReflectionException
     */
    public function testTraitGetterSetter(): void
    {
        $provider = $this->createProvider();
        $entity = new HasProviderBlankStub();
        $entity->setProvider($provider);

        self::assertSame($provider, $entity->getProvider());
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
