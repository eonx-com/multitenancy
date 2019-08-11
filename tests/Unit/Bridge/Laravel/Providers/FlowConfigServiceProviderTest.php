<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Providers;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\FlowConfig;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ServiceProviderTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider
 */
class FlowConfigServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            FlowConfigInterface::class => FlowConfig::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getServiceProvider(): string
    {
        return FlowConfigServiceProvider::class;
    }
}