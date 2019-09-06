<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Requests\Providers;

use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\RequestTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Http\Requests\BaseRequest
 * @covers \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest
 */
final class ProviderCreateRequestTest extends RequestTestCase
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
        $json = <<<'JSON'
{}
JSON;

        $expected = [
            'id' => [
                'This value should not be blank.',
            ],
            'name' => [
                'This value should not be blank.',
            ],
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
        $json = <<<'JSON'
{
    "id": false,
    "name": false
}
JSON;

        $expected = [
            'id' => [
                'This value should be of type string.',
            ],
            'name' => [
                'This value should be of type string.',
            ],
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
        $longId = \str_repeat('x', 101);
        $longName = \str_repeat('x', 256);

        $json = <<<JSON
{
    "id": "{$longId}",
    "name": "{$longName}" 
}
JSON;

        $expected = [
            'id' => [
                'This value is too long. It should have 100 characters or less.',
            ],
            'name' => [
                'This value is too long. It should have 255 characters or less.',
            ],
        ];

        $result = $this->buildFailingRequest($json);

        self::assertSame($expected, $result);
    }

    /**
     * Test successful object creation.
     *
     * @return void
     */
    public function testSuccessfulCreation(): void
    {
        $json = <<<'JSON'
{
    "id": "10",
    "name": "test"
}
JSON;

        $result = $this->buildValidatedRequest($json);

        self::assertInstanceOf(ProviderCreateRequest::class, $result);
        /** @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest $result */
        self::assertSame('10', $result->getId());
        self::assertSame('test', $result->getName());
    }
}
