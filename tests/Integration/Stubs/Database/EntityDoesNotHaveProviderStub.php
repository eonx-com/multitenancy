<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Integration\Stubs\Database;

use Doctrine\ORM\Mapping as ORM;

/**
 * This stub has a repository with the right interface.
 *
 * @ORM\Entity()
 */
final class EntityDoesNotHaveProviderStub
{
    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     *
     * @var string Internal Database ID.
     */
    private $entityId;

    /**
     * Get entity id
     *
     * @return string|null
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }
}
