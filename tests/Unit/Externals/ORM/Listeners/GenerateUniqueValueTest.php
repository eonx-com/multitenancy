<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use EoneoPay\Utils\Generator;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Listeners\GenerateUniqueValue;
use ReflectionClass;
use ReflectionException;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityWithGenerateUniqueValueCallbackInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityWithGenerateUniqueValueInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\ProviderResolver\ProviderResolverStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Utils\GeneratorStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Listeners\GenerateUniqueValue
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required to fully test listener
 */
final class GenerateUniqueValueTest extends DoctrineTestCase
{
    /**
     * Test callback is invoked if it exists.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If no value generated
     */
    public function testGenerationWithCallbackInvokesCallback(): void
    {
        $entity = new EntityWithGenerateUniqueValueCallbackInterfaceStub();

        // Invoke generator
        $generator = new GenerateUniqueValue(new Generator(), new ProviderResolverStub());
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getEntityManager()));

        // Callback should overwrite generated value
        self::assertSame('callback', $entity->getGeneratedValue());
    }

    /**
     * Test generation with check digit consitently adds the same check digit.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If no value generated
     */
    public function testGenerationWithCheckDigit(): void
    {
        $stub = new GeneratorStub();
        $entity = new EntityWithGenerateUniqueValueInterfaceStub(true);

        // Invoke generate with known 'random' string
        $generator = new GenerateUniqueValue($stub, new ProviderResolverStub());
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getEntityManager()));

        self::assertSame('notrandom3', (string)$entity->getGeneratedValue());
    }

    /**
     * Ensure generator will only execute against entities that have the interface implemented.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If no value generated
     */
    public function testGeneratorSkipsNoInterface(): void
    {
        $entity = new EntityHasProviderStub('entityId', 'test');

        $expected = $this->getEntityContents($entity);

        // Invoke generator
        $generator = new GenerateUniqueValue(new Generator(), new ProviderResolverStub());
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getEntityManager()));

        // Remove entity id
        $actual = $this->getEntityContents($entity);
        $actual['entityId'] = null;

        self::assertSame($expected, $actual);
    }

    /**
     * Test pre-persist method works as expeceted.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     */
    public function testPrePersist(): void
    {
        $entity = new EntityWithGenerateUniqueValueInterfaceStub();

        self::assertNull($entity->getGeneratedValue());

        // Create generator and add to event manager
        $generator = new GenerateUniqueValue(new Generator(), new ProviderResolverStub());
        $this->getEntityManager()->getEventManager()->addEventListener([Events::prePersist], $generator);

        // Persist entity
        $entityManager = new EntityManager($this->getEntityManager());
        $entityManager->persist($this->createProvider('provider'), $entity);

        // Ensure value was generated
        self::assertSame(9, \mb_strlen((string)$entity->getGeneratedValue()));
    }

    /**
     * Ensure generator will throw an Exception after one hundred attempts of generating a unique random value.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\RepositoryClassDoesNotImplementInterfaceException If wrong interface
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\UniqueValueNotGeneratedException If no value generated
     */
    public function testPrePersistThrowsExceptionIfAttemptsExhausted(): void
    {
        $entityManager = new EntityManager($this->getEntityManager());
        $generator = new GeneratorStub();
        $provider = $this->createProvider('provider');

        $entity = new EntityWithGenerateUniqueValueInterfaceStub();
        $entityManager->persist($provider, $entity);

        // Set key to match generator stub, since stub returns the same value every time a
        // unique value will never be found and the exception should be thrown
        $entity->setGeneratedValue($generator->randomString());
        $entityManager->flush($provider);

        // For provider stub to return the right provider using a non-random unique value
        $generator = new GenerateUniqueValue($generator, new ProviderResolverStub($provider));

        $this->expectException(UniqueValueNotGeneratedException::class);

        // Attempt to generate a value but the generator should continuously return the same value
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getEntityManager()));
    }

    /**
     * Get entity contents via reflection, this is used so there's no reliance
     * on entity methods such as toArray for tests to work.
     *
     * @param object $entity The entity to get data from
     *
     * @return mixed[]
     */
    private function getEntityContents(object $entity): array
    {
        // Get properties available for this entity
        try {
            $reflection = new ReflectionClass(\get_class($entity));
        } /** @noinspection BadExceptionsProcessingInspection */ catch (ReflectionException $exception) {
            // Ignore error and return no values
            return [];
        }

        $properties = $reflection->getProperties();

        // Get property values
        $contents = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $contents[$property->name] = $property->getValue($entity);
        }

        return $contents;
    }
}
