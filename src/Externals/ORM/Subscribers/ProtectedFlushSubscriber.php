<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;

final class ProtectedFlushSubscriber implements EventSubscriber
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Database\Entities\Provider
     */
    private $provider;

    /**
     * Create protected flush subscriber.
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider to ensure owns entities
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * Ensure all entities being flushed are owned by the current provider, if applicable.
     *
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args On flush event arguments
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException If provider mismatch
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        // Get unit of work from entity manager so we can iterate through changes
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        // Get entity and collection changes
        $changes = \array_merge(...[
            $unitOfWork->getScheduledEntityDeletions(),
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates(),
            $this->getCollectionChanges($unitOfWork->getScheduledCollectionUpdates()),
            $this->getCollectionChanges($unitOfWork->getScheduledCollectionDeletions()),
        ]);

        // Check ownership of entities
        $valid = $this->checkEntityOwnership($changes, $this->provider);

        // Deregister subscriber so it isn't triggered again next time
        $entityManager->getEventManager()->removeEventSubscriber($this);

        // If ownership is valid, return
        if ($valid === true) {
            return;
        }

        // Throw exception due to invalid ownership
        throw new InvalidEntityOwnershipException();
    }

    /**
     * Check the ownership of entities.
     *
     * @param object[] $entities Entities that are scheduled for an update
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider entities should belong to
     *
     * @return bool
     */
    private function checkEntityOwnership(array $entities, Provider $provider): bool
    {
        foreach ($entities as $entity) {
            // Skip check if the entity doesn't implement provider trait
            if (\in_array(HasProviderInterface::class, \class_implements($entity), true) === false) {
                continue;
            }

            /**
             * @var \LoyaltyCorp\Multitenancy\Database\Traits\HasProvider $entity
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chec
             */
            $owner = $entity->getProvider();

            // If the provider isn't specified or the id mismatches, throw exception
            if (($owner instanceof Provider) === false || $owner !== $provider) {
                return false;
            }
        }

        // If no return has happened, entities are all good
        return true;
    }

    /**
     * Get entities from a collection for checking.
     *
     * @param \Doctrine\Common\Collections\Collection[] $collections Collection changes to check
     *
     * @return object[]
     */
    private function getCollectionChanges(array $collections): array
    {
        $changes = [];

        foreach ($collections as $collection) {
            $changes[] = $collection->toArray();
        }

        return \count($changes) > 0 ? \array_merge(...$changes) : [];
    }
}
