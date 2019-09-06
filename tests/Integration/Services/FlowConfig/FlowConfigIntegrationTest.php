<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Services\FlowConfig;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Providers\FlowConfigServiceProvider;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\FlowConfig;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\FlowConfigEntityStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @coversNothing
 */
final class FlowConfigIntegrationTest extends AppTestCase
{
    /**
     * Test config values are not flushed automatically.
     * This is because the library is setup to not auto flush.
     *
     * @return void
     */
    public function testConfigIsNotFlushedAutomatically(): void
    {
        $flowConfig = $this->getFlowConfig();
        $flowConfig->set('key_1', 'value_1');

        $entity = new FlowConfigEntityStub('user_id');
        $flowConfig->setByEntity($entity, 'key_2', 'value_2');

        self::assertNull($flowConfig->get('key_1'));
        self::assertNull($flowConfig->getByEntity($entity, 'key_2'));
    }

    /**
     * Test integration with real flow config library.
     *
     * @return void
     */
    public function testIntegrationWithRealFlowConfig(): void
    {
        $flowConfig = $this->getFlowConfig();
        $flowConfig->set('key_1', 'value_1');

        $entity = new FlowConfigEntityStub('user_id');
        $flowConfig->setByEntity($entity, 'key_2', 'value_2');

        // flush the changes.
        $this->getEntityManager()->flush();

        $actualValueKey1 = $flowConfig->get('key_1');
        $actualValueKey2 = $flowConfig->getByEntity($entity, 'key_2');

        self::assertSame('value_1', $actualValueKey1);
        self::assertSame('value_2', $actualValueKey2);
    }

    /**
     * Test that provider is considered a real entity for flow config use.
     *
     * @return void
     */
    public function testProviderIntegrationWithRealFlowConfig(): void
    {
        $flowConfig = $this->getFlowConfig();
        $flowConfig->set('key_1', 'value_1');

        $entity = new Provider('id', 'name');
        $flowConfig->setByEntity($entity, 'key_2', 'value_2');

        // flush the changes.
        $this->getEntityManager()->flush();

        $actualValueKey1 = $flowConfig->get('key_1');
        $actualValueKey2 = $flowConfig->getByEntity($entity, 'key_2');

        self::assertSame('value_1', $actualValueKey1);
        self::assertSame('value_2', $actualValueKey2);
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
