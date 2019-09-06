<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers;

use CodeFoundation\FlowConfig\Repository\DoctrineConfig;
use CodeFoundation\FlowConfig\Repository\DoctrineEntityConfig;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\FlowConfig;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface;

final class FlowConfigServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(FlowConfigInterface::class, static function (Container $app): FlowConfig {
            $entityManager = $app->make('registry')->getManager();
            $autoFlush = false;

            return new FlowConfig(
                new DoctrineEntityConfig($entityManager, $autoFlush),
                new DoctrineConfig($entityManager, $autoFlush)
            );
        });
    }
}
