<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM;

use Doctrine\ORM\EntityRepository;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager
 */
final class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Test protected flush bypasses entities which don't have the HasProvider trait
     *
     * @return void
     */
    public function testEntityManagerFlushBypassesEntitiesWithoutProviderTrait(): void
    {
        $instance = $this->createInstance();

        // Create a provider, flush should ignore
        $provider = new Provider('external', 'Acme Corp');
        $instance->persist($provider);
        $instance->flush($provider);

        // No exception should be thrown and an id should have been generated
        self::assertNotNull($provider->getProviderId());
        self::assertIsInt($provider->getProviderId());
    }

    /**
     * Test protected flush throws exception if there is an entity with an mismatched
     * provider id from what is passed to the flush method
     *
     * @return void
     */
    public function testEntityManagerFlushThrowsExceptionIfProviderIdMismatchOnEntity(): void
    {
        $instance = $this->createInstance();

        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create three entities, the third of which is using a different provider
        $entity1 = new EntityHasProviderStub('entity1', 'Acme Corp');
        $entity1->setProvider($provider1);
        $instance->persist($entity1);
        $entity2 = new EntityHasProviderStub('entity2', 'Acme Corp');
        $entity2->setProvider($provider1);
        $instance->persist($entity2);
        $entity3 = new EntityHasProviderStub('entity3', 'Acme Corp');
        $entity3->setProvider($provider2);
        $instance->persist($entity3);

        // Set expectation
        $this->expectException(InvalidEntityOwnershipException::class);

        // Attempt to flush
        $instance->flush($provider1);
    }

    /**
     * Test get repository method
     *
     * @return void
     */
    public function testGetRepository(): void
    {
        $instance = $this->createInstance();

        $repository = $instance->getRepository(EntityHasProviderStub::class);

        self::assertInstanceOf(EntityRepository::class, $repository);
    }

    /**
     * Test remove method
     *
     * @return void
     */
    public function testRemove(): void
    {
        $instance = $this->createInstance();

        // Create provider
        $provider = $this->createProvider('provider');

        // Create entity
        $entity = new EntityHasProviderStub('entity', 'Acme Corp');
        $entity->setProvider($provider);
        $instance->persist($entity);
        $instance->flush($provider);

        // Capture entity id
        $entityId = (string)$entity->getEntityId();

        // Create repository
        $repository = $instance->getRepository(EntityHasProviderStub::class);

        // Make sure entity can be found
        $found = $repository->find($entityId);
        self::assertSame($entity, $found);

        // Remove entity
        $instance->remove($found);
        $instance->flush($provider);

        // Check it no longer exists
        $found = $repository->find($entityId);
        self::assertNull($found);
    }

    /**
     * Get entity manager instance
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface
     */
    protected function createInstance(): EntityManagerInterface
    {
        return new EntityManager($this->getEntityManager());
    }

    /**
     * Create a provider entity for testing
     *
     * @param string $externalId External id for the provider
     *
     * @return \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    private function createProvider(string $externalId): Provider
    {
        $provider = new Provider($externalId, 'Acme Corp');
        $this->getEntityManager()->persist($provider);
        $this->getEntityManager()->flush();

        return $provider;
    }
}
