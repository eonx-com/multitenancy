<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Requests;

use LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderCreateRequest;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\RequestTestCase;

class ProviderCreateRequestTest extends RequestTestCase
{
    /**
     * Returns the class to be tested.
     *
     * @return string
     */
    public function getRequestClass(): string
    {
        return ProviderCreateRequest::class;
    }

    public function testFailingEmptyJson(): void
    {
        $json = <<<JSON
{}
JSON;

        $expected = [
            'id' => [
                'This value should not be blank.',
                'This value should be of type string.'
            ],
            'name' => [
                'This value should not be blank.',
                'This value should be of type string.'
            ]
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }
}
