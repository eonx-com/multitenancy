<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;

final class InvalidRepositoryException extends RuntimeException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_RUNTIME + 29;
    }

    /**
     * Get Error sub-code.
     *
     * @return int
     */
    public function getErrorSubCode(): int
    {
        return 10;
    }
}
