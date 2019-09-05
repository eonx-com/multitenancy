<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions;

use EoneoPay\Utils\Exceptions\CriticalException;

final class ORMException extends CriticalException
{
    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return self::DEFAULT_ERROR_CODE_CRITICAL + 29;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage(): string
    {
        return 'A database error occurred.';
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
