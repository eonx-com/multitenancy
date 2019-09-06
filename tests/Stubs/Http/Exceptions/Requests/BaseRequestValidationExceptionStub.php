<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Exceptions\Requests;

use LoyaltyCorp\Multitenancy\Http\Exceptions\Requests\BaseRequestValidationException;

final class BaseRequestValidationExceptionStub extends BaseRequestValidationException
{
    /**
     * {@inheritdoc}
     */
    public function getErrorCode(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorSubCode(): int
    {
        return 1;
    }
}
