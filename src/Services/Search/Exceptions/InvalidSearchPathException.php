<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Search\Exceptions;

use EoneoPay\Utils\Exceptions\RuntimeException;

final class InvalidSearchPathException extends RuntimeException
{
    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return 110;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
