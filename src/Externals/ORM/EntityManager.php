<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityException;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Query\FilterCollection;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Coupling is required when introducing provider
 */
final class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager.
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager.
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
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     */
    public function find(Provider $provider, string $className, $entityId): ?object
    {
        return $this->getRepository($className)->find($provider, $entityId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Database\Exceptions\InvalidEntityException If entity is invalid for method
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If entity uses composite identifiers
     */
    public function findByIds(Provider $provider, $entity, array $ids): array
    {
        // We only want a class or a string as entity and it must implement the correct interface
        if ((\is_string($entity) === false && \is_object($entity) === false) ||
            \in_array(HasProviderInterface::class, \class_implements($entity), true) === false) {
            throw new InvalidEntityException(\sprintf(
                'Entity must implement %s to use findByIds().',
                HasProviderInterface::class
            ));
        }

        // Determine class name
        $class = \is_string($entity) === true ? $entity : \get_class($entity);

        $metadata = $this->entityManager->getClassMetadata($class);

        try {
            $field = \sprintf('e.%s', $metadata->getSingleIdentifierFieldName());
        } catch (MappingException $exception) {
            // Exception only thrown when composite identifiers are used
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }

        // Create query
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select('e')
            ->from($class, 'e')
            ->where('IDENTITY(e.provider) = :provider')
            ->andWhere($builder->expr()->in($field, ':ids'))
            ->setParameter('provider', $provider)
            ->setParameter('ids', $ids);

        return $builder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function flush(Provider $provider): void
    {
        $eventManager = $this->entityManager->getEventManager();

        // Add subscriber which will check the entities
        $protectedFlush = new ProtectedFlushSubscriber($provider);
        $eventManager->addEventSubscriber($protectedFlush);

        // Attempt to perform the flush
        $this->entityManager->flush();

        // Clean up by removing the subscriber
        $eventManager->removeEventSubscriber($protectedFlush);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata(string $className): ClassMetadata
    {
        return $this->entityManager->getClassMetadata($className);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollection($this->entityManager->getFilters());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\RepositoryDoesNotImplementInterfaceException Wrong int
     */
    public function getRepository(string $class)
    {
        $repository = $this->entityManager->getRepository($class);

        // Repository must be a multitenancy repository or die
        if (($repository instanceof RepositoryInterface) === false) {
            throw new RepositoryDoesNotImplementInterfaceException(\sprintf(
                'Invalid repository. %s does not implement multitenancy repository interface.',
                \get_class($repository)
            ));
        }

        /**
         * @var \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface $repository
         *
         * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === check
         */
        return $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(Provider $provider, HasProviderInterface $entity): void
    {
        // Force provider on entity
        $entity->setProvider($provider);

        $this->entityManager->merge($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Provider $provider, HasProviderInterface $entity): void
    {
        // Force provider on entity
        $entity->setProvider($provider);

        $this->entityManager->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Provider $provider, HasProviderInterface $entity): void
    {
        // Force provider on entity
        $entity->setProvider($provider);

        $this->entityManager->remove($entity);
    }
}
