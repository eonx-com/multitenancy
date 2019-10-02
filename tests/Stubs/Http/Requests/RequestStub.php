<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Requests;

use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\Exceptions\Requests\BaseRequestValidationExceptionStub;

/**
 * @coversNothing
 */
final class RequestStub implements RequestObjectInterface
{
    /**
     * Test property.
     *
     * @var string
     */
    private $property = 'test';

    /**
     * {@inheritdoc}
     */
    public static function getExceptionClass(): string
    {
        return BaseRequestValidationExceptionStub::class;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValidationGroups(): array
    {
        return [];
    }

    /**
     * Returns property.
     *
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }
}
