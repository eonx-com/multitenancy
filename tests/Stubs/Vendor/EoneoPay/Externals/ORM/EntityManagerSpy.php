<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\Query\FilterCollectionStub;

final class EntityManagerSpy implements EntityManagerInterface
{
    /**
     * If it's flushed.
     *
     * @var bool
     */
    private $flushed = false;

    /**
     * Entities that have been historically flushed.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    private $flushedEntities = [];

    /**
     * If it's persisted.
     *
     * @var bool
     */
    private $persisted = false;

    /**
     * Entities that have been historically persisted.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    private $persistedEntities = [];

    /**
     * Entities that have been removed.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    private $removedEntities = [];

    /**
     * Clear spy flags
     * This should be called (before persist() or flush()) in conjuction with isPersisted() or isFlushed().
     *
     * @return void
     */
    public function clearSpyStatus(): void
    {
        $this->persisted = false;
        $this->flushed = false;
    }

    /**
     * {@inheritdoc}
     */
    public function findByIds(string $class, array $ids): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function flush(): void
    {
        $this->flushedEntities = $this->persistedEntities;

        $this->flushed = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): FilterCollectionInterface
    {
        return new FilterCollectionStub();
    }

    /**
     * Entities that have been flushed.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    public function getFlushed(): array
    {
        return $this->flushedEntities;
    }

    /**
     * Get the latest entity that has been flushed.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null
     */
    public function getLastFlushed(): ?EntityInterface
    {
        //normally only the last entity is in concern
        $count = \count($this->flushedEntities);
        if ($count === 0) {
            return null;
        }

        return $this->flushedEntities[$count - 1];
    }

    /**
     * Get the latest entity that has been persisted.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null
     */
    public function getLastPersisted(): ?EntityInterface
    {
        //normally only the last entity is in concern
        $count = \count($this->persistedEntities);
        if ($count === 0) {
            return null;
        }

        return $this->persistedEntities[$count - 1];
    }

    /**
     * Entities that have been persisted.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    public function getPersisted(): array
    {
        return $this->persistedEntities;
    }

    /**
     * Entities that have been removed.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]
     */
    public function getRemovedEntities(): array
    {
        return $this->removedEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class)
    {
    }

    /**
     * If it's flushed.
     *
     * @return bool
     */
    public function isFlushed(): bool
    {
        return $this->flushed;
    }

    /**
     * If it's persisted.
     *
     * @return bool
     */
    public function isPersisted(): bool
    {
        return $this->persisted;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(EntityInterface $entity): void
    {
    }

    /**
     * {@inheritdoc}
     * Set as persisted.
     */
    public function persist(EntityInterface $entity): void
    {
        $this->persistedEntities[] = $entity;

        $this->persisted = true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(EntityInterface $entity): void
    {
        $this->removedEntities[] = $entity;
    }

    /**
     * Reset all spy flags and tracked entities
     * This is just for redundancy purpose, normally no need to call.
     *
     * @return void
     */
    public function resetSpy(): void
    {
        $this->persisted = false;
        $this->flushed = false;

        $this->persistedEntities = [];
        $this->flushedEntities = [];
        $this->removedEntities = [];
    }
}
