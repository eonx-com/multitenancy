<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $classMetadata;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Initialise a new repository
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager Entity manager instance
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata The class descriptor
     */
    public function __construct(EntityManagerInterface $entityManager, ClassMetadata $classMetadata)
    {
        $this->classMetadata = $classMetadata;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If there is a db or Doctrine ORM error
     */
    public function count(Provider $provider, ?array $criteria = null): int
    {
        return $this->callMethod('count', $this->createCriteria($provider, $criteria));
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ShortVariable) Parameter is inherited from interface
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If entity uses composite primary key
     */
    public function find(Provider $provider, $entityId): ?object
    {
        return $this->findOneBy($provider, [$this->getIdProperty() => $entityId]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If there is a db or Doctrine ORM error
     */
    public function findAll(Provider $provider): array
    {
        return $this->findBy($provider, []);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If there is a db or Doctrine ORM error
     */
    public function findBy(
        Provider $provider,
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $found = $this->callMethod(
            'loadAll',
            $this->createCriteria($provider, $criteria),
            $orderBy,
            $limit,
            $offset
        );

        return $found ?? [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If there is a db or Doctrine ORM error
     */
    public function findOneBy(Provider $provider, array $criteria, ?array $orderBy = null): ?object
    {
        return $this->callMethod(
            'load',
            $this->createCriteria($provider, $criteria),
            null,
            null,
            [],
            null,
            1,
            $orderBy
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName(): string
    {
        return $this->classMetadata->name;
    }

    /**
     * Create query build instance
     *
     * @param string $alias The select alias
     * @param string|null $indexBy The index to use
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select($alias)
            ->from($this->classMetadata->name, $alias, $indexBy);
    }

    /**
     * Call a method on the entity manager and catch any exception
     *
     * @param string $method The method torc/ORM/Subscribers/SoftDeleteEventSubscriber.php call
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If there is a db or Doctrine ORM error
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            // Get persister
            $persister = $this->entityManager->getUnitOfWork()->getEntityPersister($this->classMetadata->name);

            $callable = [$persister, $method];

            if (\is_callable($callable)) {
                return \call_user_func_array($callable, $parameters ?? []);
            }
        } catch (Exception $exception) {
            // Wrap all thrown exceptions as an ORM exception
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }
    }

    /**
     * Create criteria forcing provider
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider The provider who should own entities
     * @param mixed[]|null $criteria The search criteria
     *
     * @return mixed[]
     */
    private function createCriteria(Provider $provider, ?array $criteria = null): array
    {
        return \array_merge($criteria ?? [], ['provider' => $provider]);
    }

    /**
     * Get id property for the underlying entity
     *
     * @return string
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If entity uses composite primary key
     */
    private function getIdProperty(): string
    {
        // Attempt to get single id column, will throw exception if composite id column is found on entity
        try {
            return $this->classMetadata->getSingleIdentifierFieldName();
        } catch (MappingException $exception) {
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }
    }
}
