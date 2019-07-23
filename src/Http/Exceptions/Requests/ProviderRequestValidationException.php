<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Exceptions\Requests;

/**
 * An exception that is thrown when validation of the Provider request object fails.
 */
class ProviderRequestValidationException extends BaseRequestValidationException
{
    /**
     * Get Error code.
     *
     * @return int
     */
    public function getErrorCode(): int
    {
        return self::ERROR_CODE_VALIDATION + 100;
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
