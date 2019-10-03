<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;

final class MultitenancyRepositoryStub implements RepositoryInterface
{
    /**
     * @var mixed[]
     */
    private $calls;

    /**
     * @var object|null
     */
    private $object;

    /**
     * Constructor.
     *
     * @param object|null $object
     */
    public function __construct(?object $object = null)
    {
        $this->object = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function count(Provider $provider, ?array $criteria = null): int
    {
    }

    /**
     * {@inheritdoc}
     */
    public function find(Provider $provider, $entityId): ?object
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(Provider $provider): array
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        Provider $provider,
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(Provider $provider, array $criteria, ?array $orderBy = null): ?object
    {
        $this->calls[] = \compact('provider', 'criteria', 'orderBy');

        return $this->object;
    }

    /**
     * @return mixed[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName(): string
    {
    }
}
