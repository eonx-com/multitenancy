<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Controllers;

use EoneoPay\ApiFormats\Bridge\Laravel\Responses\NoContentApiResponse;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Http\ControllerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\TestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Http\Controllers\BaseController
 */
class BaseControllerTest extends TestCase
{
    /**
     * Test formattedApiResponse() successfully.
     *
     * @return void
     */
    public function testFormattedApiResponse(): void
    {
        $controller = new ControllerStub();
        $response = $controller->respond();

        self::assertSame(['ok'], $response->getContent());
    }

    /**
     * Test formattedApiResponse() successfully.
     *
     * @return void
     */
    public function testNoContentApiResponse(): void
    {
        $controller = new ControllerStub();
        $response = $controller->respondNoContent();

        self::assertInstanceOf(NoContentApiResponse::class, $response);
        self::assertSame(204, $response->getStatusCode());
        self::assertArrayHasKey('test-header', $response->getHeaders());
        self::assertSame('', $response->getContent());
    }
}
