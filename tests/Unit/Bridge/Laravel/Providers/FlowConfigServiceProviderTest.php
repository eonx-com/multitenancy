<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Providers;

use LoyaltyCorp\FlowConfig\Services\FlowConfig;
use LoyaltyCorp\FlowConfig\Services\Interfaces\FlowConfigInterface;
use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ServiceProviderTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider
 */
final class FlowConfigServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getBindings(): array
    {
        return [
            FlowConfigInterface::class => FlowConfig::class,
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
