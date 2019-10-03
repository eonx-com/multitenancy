<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Requests\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;

final class InvalidProviderException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return 101;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
