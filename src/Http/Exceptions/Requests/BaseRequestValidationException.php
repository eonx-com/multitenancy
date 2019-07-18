<?php
declare(strict_types=1);

namespace LoyaltyCorp\Mulitenancy\Http\Exceptions\Requests;

use EoneoPay\Utils\Interfaces\Exceptions\ValidationExceptionInterface;
use LoyaltyCorp\RequestHandlers\Exceptions\RequestValidationException;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
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

    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     *
     * This method converts symfony constraint violation objects into a message array
     * format that closely follows the laravel validation errors format.
     */
    public function getErrors(): array
    {
        $errors = [];

        $converter = new CamelCaseToSnakeCaseNameConverter();

        foreach ($this->getViolations() as $violation) {
            /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
            $path = $converter->normalize($violation->getPropertyPath());
            $errors[$path] = $errors[$path] ?? [];

            $errors[$path][] = $violation->getMessage();
        }

        return $errors;
    }
}
