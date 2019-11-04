<?php
declare(strict_types=1);
/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection PhpMissingParentConstructorInspection */

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Externals\ORM\Repository;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;

/**
 * This stub allows the repository to be used.
 */
final class BaseRepositoryStub extends Repository
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null
     */
    private $entity;

    /**
     * BaseRepositoryStub constructor.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityInterface|null $entity
     */
    public function __construct(?EntityInterface $entity = null)
    {
        $this->entity = $entity;

        parent::__construct(new EntityManagerStub(), new ClassMetadata(''));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return $this->entity;
    }
}
