<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM\Query;

use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;

final class FilterCollectionStub implements FilterCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function disable($name): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function enable($name): void
    {
    }
}
