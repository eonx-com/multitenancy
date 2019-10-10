<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers;

/**
 * @coversNothing
 */
final class SearchableStub
{
    /**
     * Get search id for this stub.
     *
     * @return string|null
     */
    public function getSearchId(): ?string
    {
        return 'searchable';
    }

    /**
     * Convert object to an array.
     *
     * @return string[]|null
     */
    public function toArray(): ?array
    {
        return ['search' => 'body'];
    }
}
