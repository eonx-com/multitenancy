<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;

/**
 * This Stub entity is to test HasProvider trait.
 *
 * @ORM\Entity()
 * @ORM\Table(name="entity_stub")
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateField) Suppress warning about "unused" $entityId.
 */
class EntityHasProviderStub
{
    use HasProvider;

    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     *
     * @var int Internal Database ID.
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
    }
}
