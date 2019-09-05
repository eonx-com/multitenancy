<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query;

interface FilterCollectionInterface
{
    /**
     * Disables a filter.
     *
     * @param string $name Name of the filter.
     *
     * @return void.
     *
     * @phpcsSuppress EoneoPay.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function disable($name): void;

    /**
     * Enables a filter from the collection.
     *
     * @param string $name Name of the filter.
     *
     * @return void
     *
     * @phpcsSuppress EoneoPay.Commenting.FunctionComment.ScalarTypeHintMissing
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function enable($name): void;
}
