<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Requests;

use LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderModifyRequest;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\RequestTestCase;

class ProviderModifyRequestTest extends RequestTestCase
{
    /**
     * Returns the class to be tested.
     *
     * @return string
     */
    public function getRequestClass(): string
    {
        return ProviderModifyRequest::class;
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
    "name": false
}
JSON;

        $expected = [
            'name' => [
                'This value should be of type string.'
            ]
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }

    /**
     * Tests that validation fails when the supplied data exceeds length restrictions.
     *
     * @return void
     */
    public function testFailingLengths(): void
    {
        $longName = \str_repeat('x', 256);

        $json = <<<JSON
{
    "name": "{$longName}" 
}
JSON;

        $expected = [
            'name' => [
                'This value is too long. It should have 255 characters or less.'
            ]
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }
}
