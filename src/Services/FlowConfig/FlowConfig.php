<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\FlowConfig;

use CodeFoundation\FlowConfig\Interfaces\ConfigRepositoryInterface;
use CodeFoundation\FlowConfig\Interfaces\EntityConfigRepositoryInterface;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigInterface;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigurableInterface;

final class FlowConfig implements FlowConfigInterface
{
    /**
     * @var \CodeFoundation\FlowConfig\Interfaces\EntityConfigRepositoryInterface
     */
    private $entityFlowConfig;

    /**
     * @var \CodeFoundation\FlowConfig\Interfaces\ConfigRepositoryInterface
     */
    private $flowConfig;

    /**
     * FlowConfig constructor.
     *
     * @param \CodeFoundation\FlowConfig\Interfaces\EntityConfigRepositoryInterface $entityFlowConfig
     * @param \CodeFoundation\FlowConfig\Interfaces\ConfigRepositoryInterface $flowConfig
     * @param bool|null $autoFlush
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) constructor signature set for future developments.
     */
    public function __construct(
        EntityConfigRepositoryInterface $entityFlowConfig,
        ConfigRepositoryInterface $flowConfig,
        ?bool $autoFlush = null
    ) {
        $this->entityFlowConfig = $entityFlowConfig;
        $this->flowConfig = $flowConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, ?string $default = null): ?string
    {
        return $this->flowConfig->get($key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getByEntity(FlowConfigurableInterface $entity, string $key, ?string $default = null): ?string
    {
        return $this->entityFlowConfig->getByEntity($entity, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, string $value): void
    {
        $this->flowConfig->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setByEntity(FlowConfigurableInterface $entity, string $key, string $value): void
    {
        $this->entityFlowConfig->setByEntity($entity, $key, $value);
    }
}
