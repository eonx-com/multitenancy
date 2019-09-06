<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Http;

use EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface;
use LoyaltyCorp\Multitenancy\Http\Controllers\BaseController;

final class ControllerStub extends BaseController
{
    /**
     * Returns a simple 200 repsonse.
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     */
    public function respond(): FormattedApiResponseInterface
    {
        return $this->formattedApiResponse(['ok']);
    }

    /**
     * Returns a response with no content.
     *
     * @return \EoneoPay\ApiFormats\Interfaces\FormattedApiResponseInterface
     */
    public function respondNoContent(): FormattedApiResponseInterface
    {
        return $this->noContentApiResponse(204, ['test-header' => 'some-value']);
    }
}
