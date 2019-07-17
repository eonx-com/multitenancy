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

    /**
     * Tests that an empty JSON object causes validation to fail with specific messages.
     *
     * @return void
     */
    public function testFailingEmptyJson(): void
    {
        $json = <<<JSON
{}
JSON;

        $expected = [
            'id' => [
                'This value should not be blank.'
            ],
            'name' => [
                'This value should not be blank.'
            ]
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }

    /**
     * Tests that invalid data types causes validation to fail with specific messages.
     *
     * @return void
     */
    public function testFailingInvalidDataTypes(): void
    {
        $json = <<<JSON
{
    "id": false,
    "name": false
}
JSON;

        $expected = [
            'id' => [
                'This value should be of type string.'
            ],
            'name' => [
                'This value should be of type string.'
            ]
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }
}
