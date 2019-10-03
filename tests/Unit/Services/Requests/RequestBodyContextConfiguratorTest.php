<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Requests;

use FOS\RestBundle\Context\Context;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator
 */
final class RequestBodyContextConfiguratorTest extends AppTestCase
{
    /**
     * Tests that the configurator correctly sets the provider on the serialiser context.
     *
     * @return void
     */
    public function testConfigure(): void
    {
        $configurator = new RequestBodyContextConfigurator();

        $provider = new Provider('id', 'name');

        $request = new Request();
        $request->attributes->set('provider', $provider);

        $context = new Context();

        $configurator->configure($context, $request);

        self::assertSame(
            $provider,
            $context->getAttribute(RequestBodyContextConfigurator::MULTITENANCY_PROVIDER)
        );
    }

    /**
     * Tests that the configurator ignores invalid data.
     *
     * @return void
     */
    public function testConfigureInvalidData(): void
    {
        $configurator = new RequestBodyContextConfigurator();

        $request = new Request();
        $request->attributes->set('provider', 'not a provider');

        $context = new Context();

        $configurator->configure($context, $request);

        self::assertNull($context->getAttribute(RequestBodyContextConfigurator::MULTITENANCY_PROVIDER));
    }

    /**
     * Tests that the configurator ignores invalid data.
     *
     * @return void
     */
    public function testConfigureNoProviderKey(): void
    {
        $configurator = new RequestBodyContextConfigurator();

        $request = new Request();

        $context = new Context();

        $configurator->configure($context, $request);

        self::assertNull($context->getAttribute(RequestBodyContextConfigurator::MULTITENANCY_PROVIDER));
    }
}
