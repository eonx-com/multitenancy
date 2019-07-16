<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Exceptions\Requests;

use LoyaltyCorp\Mulitenancy\Http\Exceptions\Requests\BaseRequestValidationException;

class BaseRequestValidationExceptionStub extends BaseRequestValidationException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return 1;
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
