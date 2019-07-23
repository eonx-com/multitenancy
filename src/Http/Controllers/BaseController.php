<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Http\Controllers;

use EoneoPay\ApiFormats\Bridge\Laravel\Responses\FormattedApiResponse;
use EoneoPay\ApiFormats\Bridge\Laravel\Responses\NoContentApiResponse;
use EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren) All controllers extend this class
 */
abstract class BaseController
{
    /**
     * Create formatted api response for given content.
     *
     * @param \EoneoPay\Utils\Interfaces\SerializableInterface|mixed[] $content
     * @param int|null $statusCode
     * @param string[]|null $headers
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     */
    protected function formattedApiResponse(
        $content,
        ?int $statusCode = null,
        ?array $headers = null
    ): FormattedApiResponseInterface {
        return new FormattedApiResponse($content, $statusCode ?? 200, $headers ?? []);
    }

    /**
     * Create no content api response.
     *
     * @param int|null $statusCode
     * @param string[]|null $headers
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     */
    protected function noContentApiResponse(
        ?int $statusCode = null,
        ?array $headers = null
    ): FormattedApiResponseInterface {
        return new NoContentApiResponse($statusCode ?? 204, $headers ?? []);
    }
}
