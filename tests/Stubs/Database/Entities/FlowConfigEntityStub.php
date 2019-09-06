<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Services\FlowConfig\Interfaces\FlowConfigurableInterface;

/**
 * @ORM\Entity()
 */
class FlowConfigEntityStub implements FlowConfigurableInterface
{
    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Id()
     *
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
        return 'stub';
    }
}
