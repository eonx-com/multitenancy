<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Interfaces\Repositories;

interface ProviderAwareActivityRepositoryInterface
{
    /**
     * Returns a fill iterable.
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface[]
     */
    public function getFillIterable(): iterable;
}
