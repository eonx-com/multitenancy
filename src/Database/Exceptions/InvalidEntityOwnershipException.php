<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;

final class InvalidEntityOwnershipException extends RuntimeException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return 5000;
    }

    /**
     * Get Error sub-code.
     *
     * @return int
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
