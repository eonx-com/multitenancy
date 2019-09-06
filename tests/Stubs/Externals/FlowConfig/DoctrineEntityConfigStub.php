<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\FlowConfig;

use CodeFoundation\FlowConfig\Interfaces\EntityConfigRepositoryInterface;
use CodeFoundation\FlowConfig\Interfaces\EntityIdentifier;

final class DoctrineEntityConfigStub implements EntityConfigRepositoryInterface
{
    /**
     * @var mixed[]
     */
    private $configs;

    /**
     * DoctrineEntityConfigStub constructor.
     *
     * @param mixed[]|null $configs
     */
    public function __construct(?array $configs = null)
    {
        $this->configs = $configs ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function canSetByEntity(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getByEntity(EntityIdentifier $entity, string $key, $default = null)
    {
        $entityConfig = $this->configs[$entity->getEntityId()] ?? [];

        return $entityConfig[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setByEntity(EntityIdentifier $entity, string $key, $value): void
    {
        $entityConfig = $this->configs[$entity->getEntityId()] ?? [];
        $entityConfig = \array_merge($entityConfig, [$key => $value]);

        $this->configs = \array_merge(
            $this->configs,
            [$entity->getEntityId() => $entityConfig]
        );
    }
}
