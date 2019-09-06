<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Externals\ORM\Subscribers;

use Doctrine\ORM\Event\OnFlushEventArgs;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\DoctrineTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber
 */
final class ProtectedFlushSubscriberTest extends DoctrineTestCase
{
    /**
     * Test the collections are checked for provider when cascading is automatic.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function testCollectionsAreCheckedForProviderMatches(): void
    {
        $entityManager = $this->getEntityManager();

        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create subscriber and bind to event manager
        $instance = $this->createInstance($provider1);
        $entityManager->getEventManager()->addEventSubscriber($instance);

        // Create two entities with different providers
        $entity1 = new EntityHasProviderStub('entity1', 'Acme Corp');
        $entity1->setProvider($provider1);
        $entityManager->persist($entity1);
        $entity2 = new EntityHasProviderStub('entity2', 'Acme Corp');
        $entity2->setProvider($provider2);
        $entity1->getOwned()->add($entity2);

        // Set expectation
        $this->expectException(InvalidEntityOwnershipException::class);

        // Flush should throw exception
        $entityManager->flush();
    }

    /**
     * Test protected flush bypasses entities which don't have the HasProvider trait.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException If provider mismatch
     */
    public function testSubscriberBypassesEntitiesWithoutProviderTrait(): void
    {
        // Create a provider, flush should ignore it as it doesn't have the HasProvider trait
        $provider = new Provider('external', 'Acme Corp');
        $this->getEntityManager()->persist($provider);

        // Create subscriber
        $instance = $this->createInstance($provider);

        // Create arguments from entity manager with pending persist
        $arguments = new OnFlushEventArgs($this->getEntityManager());

        // Run subscriber
        $instance->onFlush($arguments);

        // If no exception was thrown, test was successful
        $this->addToAssertionCount(1);
    }

    /**
     * Test protected flush throws exception if there is an entity with an mismatched
     * provider id from what is passed to the flush method.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException If provider mismatch
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function testSubscriberThrowsExceptionIfProviderIdMismatchOnEntity(): void
    {
        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create subscriber
        $instance = $this->createInstance($provider1);

        // Create three entities, the third of which is using a different provider
        $entity1 = new EntityHasProviderStub('entity1', 'Acme Corp');
        $entity1->setProvider($provider1);
        $this->getEntityManager()->persist($entity1);
        $entity2 = new EntityHasProviderStub('entity2', 'Acme Corp');
        $entity2->setProvider($provider1);
        $this->getEntityManager()->persist($entity2);
        $entity3 = new EntityHasProviderStub('entity3', 'Acme Corp');
        $entity3->setProvider($provider2);
        $this->getEntityManager()->persist($entity3);

        // Create arguments from entity manager with pending persists
        $arguments = new OnFlushEventArgs($this->getEntityManager());

        // Set expectation
        $this->expectException(InvalidEntityOwnershipException::class);

        // Run subscriber
        $instance->onFlush($arguments);
    }

    /**
     * Test subscriber unsets itself once successfully run.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function testSubscriberUnsetsItselfAfterSuccessfulRun(): void
    {
        $entityManager = $this->getEntityManager();

        // Create provider
        $provider = $this->createProvider('provider');

        // Create subscriber
        $instance = $this->createInstance($provider);

        // Get event manager and bind subscriber
        $eventManager = $entityManager->getEventManager();
        $eventManager->addEventSubscriber($instance);

        // Create a valid entity
        $entity = new EntityHasProviderStub('entity', 'Acme Corp');
        $entity->setProvider($provider);
        $entityManager->persist($entity);

        // Ensure subscriber is bound
        self::assertContains($instance, $eventManager->getListeners('onFlush'));

        // Flush changes
        $entityManager->flush();

        // Ensure subscriber has unset itself
        self::assertNotContains($instance, $eventManager->getListeners('onFlush'));
    }

    /**
     * Test subscriber unsets itself even if an exception is thrown.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\ProviderAlreadySetException If provider clashes
     */
    public function testSubscriberUnsetsItselfBeforeThrowningException(): void
    {
        $entityManager = $this->getEntityManager();

        // Create two providers
        $provider1 = $this->createProvider('provider1');
        $provider2 = $this->createProvider('provider2');

        // Create subscriber
        $instance = $this->createInstance($provider1);

        // Get event manager and bind subscriber
        $eventManager = $entityManager->getEventManager();
        $eventManager->addEventSubscriber($instance);

        // Create an invalid entity with the wrong provider
        $entity = new EntityHasProviderStub('entity', 'Acme Corp');
        $entity->setProvider($provider2);
        $entityManager->persist($entity);

        // Ensure subscriber is bound
        self::assertContains($instance, $eventManager->getListeners('onFlush'));

        // Flush changes which should thrown an exception
        try {
            $entityManager->flush();
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (InvalidEntityOwnershipException $exception) {
            // Ensure subscriber has unset itself
            self::assertNotContains($instance, $eventManager->getListeners('onFlush'));
            self::assertSame('', $exception->getMessage());

            // Return so the following failure doesn't happen
            return;
        }

        // If exception wasn't thrown, fail test
        self::fail(\sprintf('Expected %s to be thrown but it did not happen', InvalidEntityOwnershipException::class));
    }

    /**
     * Get entity manager instance.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider
     *
     * @return \LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber
     */
    protected function createInstance(Provider $provider): ProtectedFlushSubscriber
    {
        return new ProtectedFlushSubscriber($provider);
    }
}
