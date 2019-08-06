<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Entity;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;

/**
 * This Stub entity is to test HasProvider trait.
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class EntityHasProviderStub extends Entity
{
    use HasProvider;

    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     *
     * @var string Internal Database ID.
     */
    private $entityId;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     *
     * @var string The immutable external ID.
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * EntityHasProviderStub constructor.
     *
     * @param string $externalId
     * @param string $name
     */
    public function __construct(string $externalId, string $name)
    {
        $this->externalId = $externalId;
        $this->name = $name;

        parent::__construct();
    }

    /**
     * Get entity id
     *
     * @return string|null
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdProperty(): string
    {
        return 'entityId';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return [];
    }
}
