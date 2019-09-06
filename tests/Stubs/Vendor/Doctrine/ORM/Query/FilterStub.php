<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

final class FilterStub extends SQLFilter
{
    /**
     * Add filter constraint.
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadata $targetEntity The entity to add the constraint to
     * @param mixed|string $targetTableAlias The target table
     *
     * @return string
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        return $targetEntity->name ?? $targetTableAlias;
    }
}
