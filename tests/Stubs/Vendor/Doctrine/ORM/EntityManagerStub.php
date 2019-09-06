<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\ResultSetMapping;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;

/**
 * @SuppressWarnings(PHPMD) Doctrine interface requires this implementation
 */
final class EntityManagerStub implements EntityManagerInterface
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
     * Create entity manager stub.
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
    public function contains($object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function copy($entity, $deep = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedNativeQuery($name)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNamedQuery($name)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($dql = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder()
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
     *
     * @SuppressWarnings(PHPMD.ShortVariable) Parameter is inherited from interface
     */
    public function find($className, $id)
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
    public function getCache()
    {
    }

    /**
     * @param mixed $className
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata($className): ClassMetadata
    {
        return new ClassMetadata($className ?? EntityHasProviderStub::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
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
    public function getExpressionBuilder()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
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
     *
     * @deprecated
     */
    public function getHydrator($hydrationMode)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFactory()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialReference($entityName, $identifier)
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
    public function getProxyFactory()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ShortVariable) Parameter is inherited from interface
     */
    public function getReference($entityName, $id)
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
    public function getRepository($className)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitOfWork()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilters()
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
    public function isFiltersStateClean()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isOpen()
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
    public function merge($object)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function newHydrator($hydrationMode)
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
    public function transactional($func)
    {
    }
}
