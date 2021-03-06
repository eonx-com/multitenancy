<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityException;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityDoesNotHaveProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityDoesNotImplementRepositoryInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasCompositePrimaryKeyStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityImplementsRepositoryInterfaceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM\RepositoryStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\Query\FilterStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) High coupling required to fully test aspects of entity manager
 * @SuppressWarnings(PHPMD.TooManyPublicMethods) All tests must be public
 */
final class EntityManagerTest extends DoctrineTestCase
{
    /**
     * Test repository method findByIds.
     *
     * @return void
     */
    public function testFindByIdsFindsEntities(): void
    {
        // Create entity manager instance
        $instance = $this->createInstance($this->getEntityManager());

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
     * Test find by id fails if composite primary key used.
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
     * Test find by id fails if an entity which doesn't implement the correct interface is used.
     *
     * @return void
     */
    public function testFindByIdsThrowsExceptionIfNonProviderEntityUsed(): void
    {
        // Create entity manager instance
        $instance = $this->createInstance($this->getEntityManager());

        // Create provider
        $provider = $this->createProvider('provider');

        $this->expectException(InvalidEntityException::class);

        // Find on the composite primary key entity
        $instance->findByIds($provider, EntityDoesNotHaveProviderStub::class, []);
    }

    /**
     * Test find by id fails if something completely invalid is used for the entity.
     *
     * @return void
     */
    public function testFindByIdsThrowsExceptionIfWeirdnessPassedAsEntity(): void
    {
        // Create entity manager instance
        $instance = $this->createInstance($this->getEntityManager());

        // Create provider
        $provider = $this->createProvider('provider');

        $this->expectException(InvalidEntityException::class);

        // Mangle entity entirely
        /** @noinspection PhpParamsInspection Invalid parameter is intentional */
        $instance->findByIds($provider, [], []);
    }

    /**
     * Test repository method find.
     *
     * @return void
     */
    public function testFindEmulatesRepositoryFind(): void
    {
        // Create entity manager instance
        $instance = $this->createInstance($this->getEntityManager());

        // Create provider
        $provider = $this->createProvider('provider');

        // Create and entity
        $entity = new EntityImplementsRepositoryInterfaceStub('entity');

        // Persist entity
        $instance->persist($provider, $entity);
        $instance->flush($provider);

        // Get entity id
        $entityId = $entity->getEntityId();

        // Find using entity manager
        $entityManagerResult = $instance->find($provider, EntityImplementsRepositoryInterfaceStub::class, $entityId);

        // Find using repository
        $repositoryResult = $instance->getRepository(EntityImplementsRepositoryInterfaceStub::class)
            ->find($provider, $entityId);

        // Make sure something was found
        self::assertNotNull($entityManagerResult);
        self::assertNotNull($repositoryResult);

        // Make sure entities match
        self::assertSame($entity, $entityManagerResult);
        self::assertSame($entity, $repositoryResult);
    }

    /**
     * Test flush binds (and removes) the subscriber.
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
     * Test getting class metadata works.
     *
     * @return void
     */
    public function testGetClassMetadata(): void
    {
        // Create entity manager instance
        $instance = $this->createInstance($this->getEntityManager());

        $instance->getClassMetadata(EntityHasProviderStub::class);

        // Method is pass through only
        $this->addToAssertionCount(1);
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
     * Test get repository method.
     *
     * @return void
     */
    public function testGetRepository(): void
    {
        $instance = $this->createInstance();

        $repository = $instance->getRepository(EntityImplementsRepositoryInterfaceStub::class);

        self::assertInstanceOf(RepositoryStub::class, $repository);
    }

    /**
     * Test get repository throws an exception if interface is not implemented.
     *
     * @return void
     */
    public function testGetRepositoryOnlyAllowsRepositoryInstances(): void
    {
        $instance = $this->createInstance();

        $this->expectException(RepositoryDoesNotImplementInterfaceException::class);

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
     * Test remove method.
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
     * Get entity manager instance.
     *
     * @param \Doctrine\ORM\EntityManagerInterface|null $entityManager Entity manager to use
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface
     */
    protected function createInstance(?DoctrineEntityManager $entityManager = null): EntityManagerInterface
    {
        return new EntityManager($entityManager ?? $this->getEntityManager());
    }
}
