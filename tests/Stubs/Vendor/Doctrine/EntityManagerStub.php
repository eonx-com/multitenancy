<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

/**
 * @coversNothing
 *
 * @SuppressWarnings(PHPMD) Doctrine interface requires this implementation
 */
class EntityManagerStub implements EntityManagerInterface
{
    /**
     * @var \Doctrine\Common\EventManager
     */
    private $eventManager;

    /**
     * The number of times the entity manager was flushed.
     *
     * @var int
     */
    private $flushCount = 0;

    /**
     * Any entities that were persisted.
     *
     * @var mixed[]
     */
    private $persisted = [];

    /**
     * Any entities that were removed.
     *
     * @var mixed[]
     */
    private $removed = [];

    /**
     * Create entity manager stub
     *
     * @param \Doctrine\Common\EventManager $eventManager Event manager instance
     */
    public function __construct(?EventManager $eventManager = null)
    {
        $this->eventManager = $eventManager ?? new EventManager();
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function commit(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function contains($object): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function copy($entity, $deep = null): object
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedNativeQuery($name): NativeQuery
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedQuery($name): Query
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($dql = null): Query
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder(): QueryBuilder
    {
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function find($className, $id): ?object
    {
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $this->flushCount++;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache(): ?Cache
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata($className)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): Configuration
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(): Connection
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getEventManager(): EventManager
    {
        return $this->eventManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder(): Query\Expr
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): Query\FilterCollection
    {
    }

    /**
     * Returns the flush count.
     *
     * @return int
     */
    public function getFlushCount(): int
    {
        return $this->flushCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getHydrator($hydrationMode): AbstractHydrator
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFactory(): ClassMetadataFactory
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialReference($entityName, $identifier): ?object
    {
    }

    /**
     * Returns any entities that were persisted.
     *
     * @return mixed[]
     */
    public function getPersisted(): array
    {
        return $this->persisted;
    }

    /**
     * {@inheritdoc}
     */
    public function getProxyFactory(): ProxyFactory
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getReference($entityName, $id): ?object
    {
    }

    /**
     * Returns any entities that were removed.
     *
     * @return mixed[]
     */
    public function getRemoved(): array
    {
        return $this->removed;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository($className): ObjectRepository
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfWork(): UnitOfWork
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilters(): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function initializeObject($obj): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isFiltersStateClean(): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen(): bool
    {
    }

    /**
     * {@inheritdoc}
     */
    public function lock($entity, $lockMode, $lockVersion = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object): object
    {
    }

    /**
     * {@inheritdoc}
     */
    public function newHydrator($hydrationMode): AbstractHydrator
    {
    }

    /**
     * {@inheritdoc}
     */
    public function persist($object): void
    {
        $this->persisted[] = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object): void
    {
        $this->removed[] = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function transactional($func): void
    {
    }
}
