<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM;

use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface;

final class EntityRepositoryStub implements RepositoryInterface
{
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
     * @SuppressWarnings(PHPMD.ShortVariable) API requires property name
     */
    public function find($id)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
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
