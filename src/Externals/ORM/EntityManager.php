<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use Doctrine\ORM\Mapping\MappingException;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\InvalidRepositoryException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;
use LoyaltyCorp\Multitenancy\Externals\ORM\Query\FilterCollection;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;

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
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If entity uses composite identifiers
     */
    public function findByIds(Provider $provider, HasProviderInterface $entity, array $ids): array
    {
        $class = \get_class($entity);

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
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollection($this->entityManager->getFilters());
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\InvalidRepositoryException If repository isn't mtenant
     */
    public function getRepository(string $class)
    {
        $repository = $this->entityManager->getRepository($class);

        // Repository must be a multitenancy repository or die
        if (($repository instanceof RepositoryInterface) === false) {
            throw new InvalidRepositoryException(\sprintf(
                'Invalid repository. %s does not implement multitenancy repository interface.',
                \get_class($repository)
            ));
        }

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
