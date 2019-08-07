<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;

final class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(DoctrineEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException If provider mismatch
     */
    public function flush(Provider $provider): void
    {
        // Ensure that all entities pending write are for the correct provider
        $unitOfWork = $this->entityManager->getUnitOfWork();

        // Get entity changes
        $changes = \array_merge(... [
            $unitOfWork->getScheduledEntityDeletions(),
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates()
        ]);

        // Check ownership of entities
        $this->checkEntityOwnership($changes, $provider);

        // If no exception has been thrown, allow flush to be performed
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class)
    {
        return $this->entityManager->getRepository($class);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($entity): void
    {
        $this->entityManager->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
    }

    /**
     * Check the ownership of entities
     *
     * @param object[] $entities Entities that are scheduled for an update
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider entities should belong to
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityOwnershipException If provider mismatch
     */
    private function checkEntityOwnership(array $entities, Provider $provider): void
    {
        foreach ($entities as $entity) {
            // Skip check if the entity doesn't implement provider trait
            if (\in_array(HasProvider::class, $this->getEntityTraitsRecursive($entity), true) === false) {
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
                throw new InvalidEntityOwnershipException();
            }
        }
    }

    /**
     * Get all traits used by a class and any parent classes
     *
     * @param object $entity Entity to get traits for
     *
     * @return mixed[]
     */
    private function getEntityTraitsRecursive(object $entity): array
    {
        $results = [];

        // Determine class name
        $baseClass = \get_class($entity);

        $classes = \array_reverse(\class_parents($baseClass));
        $classes[$baseClass] = $baseClass;

        foreach ($classes as $class) {
            $results[] = $this->getTraitTraitsRecursive($class);
        }

        return \array_unique(\array_merge(...$results));
    }

    /**
     * Get all traits used by a trait, recursively
     *
     * @param string $base The base class or trait to get traits for
     *
     * @return mixed[]
     */
    private function getTraitTraitsRecursive(string $base): array
    {
        $traits = [];

        // Start with base class
        $traits[] = \class_uses($base) ?: [];

        foreach (\reset($traits) as $trait) {
            $traits[] = $this->getTraitTraitsRecursive($trait);
        }

        return \array_merge(...$traits);
    }
}
