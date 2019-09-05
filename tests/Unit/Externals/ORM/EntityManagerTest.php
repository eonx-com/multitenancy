<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\InvalidRepositoryException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityDoesNotImplementRepositoryInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasCompositePrimaryKeyStub;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityImplementsRepositoryInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\Query\FilterStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) High coupling required to fully test aspects of entity manager
 */
final class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Test repository method findByIds
     *
     * @return void
     */
    public function testFindByIdsFindsEntities(): void
    {
        // Create doctrine instance and get filters
        $doctrine = $this->getEntityManager();

        // Create entity manager instance
        $instance = $this->createInstance($doctrine);

        // Create provider
        $provider = $this->createProvider('provider');

        // Create three entities
        $entity1 = new EntityHasProviderStub('entity1', 'Acme Corp');
        $entity2 = new EntityHasProviderStub('entity2', 'Acme Corp');
        $entity3 = new EntityHasProviderStub('entity3', 'Acme Corp');

        // Persist entities
        $instance->persist($provider, $entity1);
        $instance->persist($provider, $entity2);
        $instance->persist($provider, $entity3);
        $instance->flush($provider);

        // Get ids for entities
        $ids = [];
        $ids[] = $entity1->getEntityId();
        $ids[] = $entity2->getEntityId();
        $ids[] = $entity3->getEntityId();

        $result = $instance->findByIds($provider, $entity1, $ids);

        self::assertCount(3, $result);
        self::assertContains($entity1, $result);
        self::assertContains($entity2, $result);
        self::assertContains($entity3, $result);
    }

    /**
     * Test find by id fails if composite primary key used
     *
     * @return void
     */
    public function testFindByIdsThrowsExceptionIfCompositePrimaryKeyUsed(): void
    {
        // Create doctrine instance and get filters
        $doctrine = $this->getEntityManager();

        // Create entity manager instance
        $instance = $this->createInstance($doctrine);

        // Create provider
        $provider = $this->createProvider('provider');

        // Create two entities, one with a composite primary key
        $entity1 = new EntityHasProviderStub('entity1', 'Acme Corp');
        $entity2 = new EntityHasCompositePrimaryKeyStub('entity2', 'Acme Corp');

        // Persist entities
        $instance->persist($provider, $entity1);
        $instance->persist($provider, $entity2);
        $instance->flush($provider);

        // Create some random ids since it's not really important
        $ids = ['test', 'random'];

        $this->expectException(ORMException::class);

        // Find on the composite primary key entity
        $instance->findByIds($provider, $entity2, $ids);
    }

    /**
     * Test flush binds (and removes) the subscriber
     *
     * @return void
     */
    public function testFlushBindsAndRemovesSubscriber(): void
    {
        $eventManager = new EventManagerStub();
        $entityManager = new EntityManagerStub($eventManager);
        $instance = $this->createInstance($entityManager);

        // Create provider
        $provider = $this->createProvider('provider');

        // Create entity
        $entity = new EntityHasProviderStub('entity', 'Acme Corp');
        $instance->persist($provider, $entity);
        $instance->flush($provider);

        // Check subscriber was added
        $added = false;
        foreach ($eventManager->getAddedSubscribers() as $subscriber) {
            if ($subscriber instanceof ProtectedFlushSubscriber) {
                $added = true;

                break;
            }
        }
        self::assertTrue($added);

        // Check subscriber was removed
        $removed = false;
        foreach ($eventManager->getAddedSubscribers() as $subscriber) {
            if ($subscriber instanceof ProtectedFlushSubscriber) {
                $removed = true;

                break;
            }
        }
        self::assertTrue($removed);
    }

    /**
     * Test entity manager get filters returns our filters collection.
     *
     * @return void
     */
    public function testGetFiltersReturnsFilterCollection(): void
    {
        // Create doctrine instance and get filters
        $doctrine = $this->getEntityManager();
        $doctrineFilters = $doctrine->getFilters();

        // Create filter
        $filter = new FilterStub($this->getEntityManager());

        // Add filter to doctrine
        $config = $doctrine->getConfiguration();
        $config->addFilter('test-filter', FilterStub::class);

        // Create entity manager instance and get filters
        $entityManager = $this->createInstance($doctrine);
        $filters = $entityManager->getFilters();

        // Enable filter
        $filters->enable('test-filter');

        // Test filter is returned
        self::assertEquals($filter, $doctrineFilters->getEnabledFilters()['test-filter']);
    }

    /**
     * Test get repository method
     *
     * @return void
     */
    public function testGetRepository(): void
    {
        $instance = $this->createInstance();

        $repository = $instance->getRepository(EntityImplementsRepositoryInterfaceStub::class);

        self::assertInstanceOf(RepositoryInterface::class, $repository);
    }

    /**
     * Test get repository throws an exception if interface is not implemented
     *
     * @return void
     */
    public function testGetRepositoryOnlyAllowsRepositoryInstances(): void
    {
        $instance = $this->createInstance();

        $this->expectException(InvalidRepositoryException::class);

        $instance->getRepository(EntityDoesNotImplementRepositoryInterfaceStub::class);
    }

    /**
     * Test entity manager merge data into new entity from database.
     *
     * @return void
     */
    public function testMergeEntityWithDatabase(): void
    {
        $instance = $this->createInstance();

        // Create provider
        $provider = $this->createProvider('provider');

        // Create entity
        $entity = new EntityHasProviderStub('entity1', 'Acme Corp');

        $instance->persist($provider, $entity);
        $instance->flush($provider);

        // Create a second entity with the same details as the first
        $newEntity = new EntityHasProviderStub('entity2', 'Acme Corp');
        $newEntity->setEntityId((string)$entity->getEntityId());

        // Merge into database
        $instance->merge($provider, $newEntity);

        // Ensure external id was updated on original entity
        self::assertSame('entity2', $newEntity->getExternalId());
        self::assertSame($entity->getExternalId(), $newEntity->getExternalId());
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
        $entity = new EntityImplementsRepositoryInterfaceStub('');
        $instance->persist($provider, $entity);
        $instance->flush($provider);

        // Capture entity id
        $entityId = (string)$entity->getEntityId();

        // Create repository
        $repository = $instance->getRepository(EntityImplementsRepositoryInterfaceStub::class);

        // Make sure entity can be found
        $found = $repository->find($provider, $entityId);
        self::assertSame($entity, $found);

        // Remove entity
        $instance->remove($provider, $entity);
        $instance->flush($provider);

        // Check it no longer exists
        $found = $repository->find($provider, $entityId);
        self::assertNull($found);
    }

    /**
     * Get entity manager instance
     *
     * @param \Doctrine\ORM\EntityManagerInterface|null $entityManager Entity manager to use
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface
     */
    protected function createInstance(?DoctrineEntityManager $entityManager = null): EntityManagerInterface
    {
        return new EntityManager($entityManager ?? $this->getEntityManager());
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
