<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Requests;

use LoyaltyCorp\RequestHandlers\Request\RequestObjectInterface;

abstract class BaseRequest implements RequestObjectInterface
{
    /**
     * Request DTO objects are not intended to be created manually.
     */
    private function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValidationGroups(): array
    {
        return [];
    }
}
