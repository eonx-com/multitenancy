<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Exceptions\Requests;

use EoneoPay\Utils\Interfaces\Exceptions\ValidationExceptionInterface;
use LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

abstract class BaseRequestValidationException extends RequestValidationException implements ValidationExceptionInterface
{
    /**
     * The default validation error code.
     *
     * @const int
     */
    public const ERROR_CODE_VALIDATION = 6000;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        ?ConstraintViolationListInterface $violations = null,
        ?string $message = null,
        ?array $messageParameters = null,
        ?int $code = null,
        ?Throwable $previous = null
    ) {
        parent::__construct(
            $violations ?? new ConstraintViolationList(),
            $message ?? 'exceptions.http.request_body.validation_failures',
            $messageParameters,
            $code,
            $previous
        );
    }
}
