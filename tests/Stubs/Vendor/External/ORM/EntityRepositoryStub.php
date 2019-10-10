<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM;

use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;

final class EntityRepositoryStub implements RepositoryInterface
{
    /**
     * Entities.
     *
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]|null
     */
    private $entities;

    /**
     * RepositoryStub constructor.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface[]|null $entities
     */
    public function __construct(?array $entities = null)
    {
        $this->entities = $entities;
    }

    /**
     * {@inheritdoc}
     */
    public function count(?array $criteria = null): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ShortVariable) Variable inherited from interface
     */
    public function find($id)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->entities ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
    }
}
