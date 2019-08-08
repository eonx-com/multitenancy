<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use Doctrine\ORM\EntityRepository;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;
use Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common\EventManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\EntityManager
 */
final class EntityManagerTest extends DoctrineTestCase
{
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
        $entity->setProvider($provider);
        $instance->persist($entity);
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
