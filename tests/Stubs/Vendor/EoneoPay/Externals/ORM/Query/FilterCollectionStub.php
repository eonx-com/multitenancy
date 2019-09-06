<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\Query;

use EoneoPay\Externals\ORM\Interfaces\Query\FilterCollectionInterface;

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
