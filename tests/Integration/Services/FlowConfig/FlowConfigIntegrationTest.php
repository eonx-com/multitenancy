<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Services\FlowConfig;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\FlowConfig;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @coversNothing
 */
class FlowConfigIntegrationTest extends AppTestCase
{
    /**
     * Test integration with real flow config library.
     *
     * @return void
     */
    public function testIntegrationWithRealFlowConfig(): void
    {
        $flowConfig = $this->getFlowConfig();
        $flowConfig->set('key_1', 'value_1');

        $actualValue = $flowConfig->get('key_1');

        self::assertSame('value_1', $actualValue);
    }

    /**
     * Get flow config instance from container.
     *
     * @return \LoyaltyCorp\Multitenancy\Services\FlowConfig\FlowConfig
     */
    protected function getFlowConfig(): FlowConfig
    {
        // create the doctrine schema
        $this->createSchema();

        // register flow config
        $this->app->register(FlowConfigServiceProvider::class);

        return $this->app->make(FlowConfigInterface::class);
    }
}
