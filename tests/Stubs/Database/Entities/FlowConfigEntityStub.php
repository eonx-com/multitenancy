<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigurableInterface;

class FlowConfigEntityStub implements FlowConfigurableInterface
{
    /**
     * @var string
     */
    private $entityId;

    /**
     * FlowConfigEntityStub constructor.
     *
     * @param string $entityId
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType(): string
    {
    }
}
